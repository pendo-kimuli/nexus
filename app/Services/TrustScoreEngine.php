<?php

namespace App\Services;

use App\Models\Exchange;
use App\Models\Milestone;
use App\Models\TrustScore;
use App\Models\TrustScoreLog;
use App\Models\User;

class TrustScoreEngine
{
    public function recalculate(User $user): TrustScore
    {
        $milestones = Milestone::whereHas('exchange', function ($q) use ($user) {
            $q->where('initiator_id', $user->id)->orWhere('counterpart_id', $user->id);
        })->where('status', 'completed')->with('exchange')->get();

        // Timeliness: % of milestones (that had a due date) completed on or before it
        $withDueDate = $milestones->whereNotNull('due_date');
        $onTime = $withDueDate->filter(fn ($m) => $m->completed_at && $m->completed_at->lte($m->due_date));
        $timeliness = $withDueDate->count() > 0 ? ($onTime->count() / $withDueDate->count()) * 100 : 100;

        // Rating consistency: average of ratings this user received from counterparts
        $ratingsReceived = [];
        foreach ($milestones as $m) {
            $exchange = $m->exchange;
            if ($exchange->initiator_id === $user->id && $m->counterpart_rating) {
                $ratingsReceived[] = $m->counterpart_rating;
            }
            if ($exchange->counterpart_id === $user->id && $m->initiator_rating) {
                $ratingsReceived[] = $m->initiator_rating;
            }
        }
        $ratingScore = count($ratingsReceived) > 0 ? (array_sum($ratingsReceived) / count($ratingsReceived)) * 20 : 60;

        // Profile completeness
        $fields = [$user->name, $user->email, $user->phone_number];
        $completeness = (count(array_filter($fields)) / count($fields)) * 100;

        // Dispute history
        $totalExchanges = Exchange::where('initiator_id', $user->id)->orWhere('counterpart_id', $user->id)->count();
        $disputed = Exchange::where('status', 'disputed')
            ->where(fn ($q) => $q->where('initiator_id', $user->id)->orWhere('counterpart_id', $user->id))
            ->count();
        $disputeScore = $totalExchanges > 0 ? (1 - ($disputed / $totalExchanges)) * 100 : 100;

        // Weighted overall score
        $overall = ($timeliness * 0.35) + ($ratingScore * 0.35) + ($completeness * 0.15) + ($disputeScore * 0.15);

        $trustScore = TrustScore::firstOrCreate(['user_id' => $user->id]);
        $trustScore->timeliness_score = round($timeliness, 2);
        $trustScore->rating_score = round($ratingScore, 2);
        $trustScore->completeness_score = round($completeness, 2);
        $trustScore->dispute_score = round($disputeScore, 2);
        $trustScore->score = round($overall, 2);
        $trustScore->capital_eligible = $overall >= config('nexus.trust_threshold', 70);
        $trustScore->save();

        TrustScoreLog::create([
            'user_id' => $user->id,
            'score' => $trustScore->score,
            'reason' => 'Recalculated after milestone confirmation',
        ]);

        return $trustScore;
    }
}