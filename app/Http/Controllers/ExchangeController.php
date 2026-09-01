<?php

namespace App\Http\Controllers;

use App\Models\Exchange;
use App\Models\Milestone;
use App\Services\SmsService;
use App\Services\TrustScoreEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExchangeController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'counterpart_id' => 'required|exists:users,id',
            'value_declaration_id' => 'nullable|exists:value_declarations,id',
            'title' => 'required|string|max:255',
            'contract_terms' => 'required|string',
        ]);

        $exchange = Exchange::create([
            'initiator_id' => Auth::id(),
            'counterpart_id' => $validated['counterpart_id'],
            'value_declaration_id' => $validated['value_declaration_id'] ?? null,
            'title' => $validated['title'],
            'contract_terms' => $validated['contract_terms'],
            'status' => 'pending',
        ]);

        (new SmsService())->send($exchange->counterpart, "NEXUS: {$exchange->initiator->name} proposed a new exchange: \"{$exchange->title}\". Review it in your dashboard.");

        return redirect()->route('exchanges.show', $exchange)->with('status', 'Exchange proposed. Waiting for the other party to accept.');
    }

    public function index()
    {
        $userId = Auth::id();
        $exchanges = Exchange::where('initiator_id', $userId)->orWhere('counterpart_id', $userId)
            ->with(['initiator', 'counterpart'])->latest()->get();

        return view('exchanges.index', compact('exchanges'));
    }

    public function show(Exchange $exchange)
    {
        $userId = Auth::id();
        abort_unless($userId === $exchange->initiator_id || $userId === $exchange->counterpart_id, 403);

        $exchange->load(['milestones', 'initiator', 'counterpart']);
        return view('exchanges.show', compact('exchange'));
    }

    public function accept(Exchange $exchange)
    {
        abort_unless(Auth::id() === $exchange->counterpart_id, 403);
        abort_unless($exchange->status === 'pending', 400);

        $exchange->status = 'active';
        $exchange->save();

        (new SmsService())->send($exchange->initiator, "NEXUS: {$exchange->counterpart->name} accepted your exchange \"{$exchange->title}\".");

        return back()->with('status', 'Exchange accepted. You can now add milestones.');
    }

    public function decline(Exchange $exchange)
    {
        abort_unless(Auth::id() === $exchange->counterpart_id, 403);
        abort_unless($exchange->status === 'pending', 400);

        $exchange->status = 'declined';
        $exchange->save();

        (new SmsService())->send($exchange->initiator, "NEXUS: {$exchange->counterpart->name} declined your exchange \"{$exchange->title}\".");

        return redirect()->route('exchanges.index')->with('status', 'Exchange declined.');
    }

    public function dispute(Request $request, Exchange $exchange)
    {
        $userId = Auth::id();
        abort_unless($userId === $exchange->initiator_id || $userId === $exchange->counterpart_id, 403);
        abort_unless($exchange->status === 'active', 400);

        $request->validate(['dispute_reason' => 'required|string|min:10']);

        $exchange->status = 'disputed';
        $exchange->dispute_reason = $request->dispute_reason;
        $exchange->save();

        $engine = new TrustScoreEngine();
        $engine->recalculate($exchange->initiator);
        $engine->recalculate($exchange->counterpart);

        $sms = new SmsService();
        $sms->send($exchange->initiator, "NEXUS: Exchange \"{$exchange->title}\" has been marked disputed and is under review.");
        $sms->send($exchange->counterpart, "NEXUS: Exchange \"{$exchange->title}\" has been marked disputed and is under review.");

        return back()->with('status', 'Dispute filed. An administrator will review this exchange.');
    }

    public function storeMilestone(Request $request, Exchange $exchange)
    {
        $userId = Auth::id();
        abort_unless($userId === $exchange->initiator_id || $userId === $exchange->counterpart_id, 403);
        abort_unless($exchange->status === 'active', 400);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);
        $validated['exchange_id'] = $exchange->id;
        $validated['status'] = 'pending';

        Milestone::create($validated);

        return back()->with('status', 'Milestone added.');
    }

    public function confirmMilestone(Request $request, Milestone $milestone)
    {
        $exchange = $milestone->exchange;
        $userId = Auth::id();
        abort_unless($userId === $exchange->initiator_id || $userId === $exchange->counterpart_id, 403);
        abort_unless($exchange->status === 'active', 400);

        $request->validate(['rating' => 'required|integer|min:1|max:5']);

        if ($userId === $exchange->initiator_id) {
            $milestone->initiator_confirmed_at = now();
            $milestone->initiator_rating = $request->rating;
        } else {
            $milestone->counterpart_confirmed_at = now();
            $milestone->counterpart_rating = $request->rating;
        }
        $milestone->save();

        if ($milestone->initiator_confirmed_at && $milestone->counterpart_confirmed_at) {
            $milestone->status = 'completed';
            $milestone->completed_at = now();
            $milestone->save();

            $allMilestones = $exchange->milestones()->get();
            if ($allMilestones->count() > 0 && $allMilestones->every(fn ($m) => $m->status === 'completed')) {
                $exchange->status = 'completed';
                $exchange->save();
            }

            $engine = new TrustScoreEngine();
            $engine->recalculate($exchange->initiator);
            $engine->recalculate($exchange->counterpart);

            $sms = new SmsService();
            $sms->send($exchange->initiator, "NEXUS: Milestone '{$milestone->title}' confirmed complete. Your trust score has been updated.");
            $sms->send($exchange->counterpart, "NEXUS: Milestone '{$milestone->title}' confirmed complete. Your trust score has been updated.");

            return back()->with('status', 'Milestone completed! Trust scores updated and both parties notified.');
        }

        return back()->with('status', 'Your confirmation has been recorded. Waiting on the other party.');
    }

    public function trustProfile()
    {
        $user = Auth::user();
        $trustScore = $user->trustScore;
        $threshold = config('nexus.trust_threshold', 70);

        $exchanges = Exchange::where('initiator_id', $user->id)->orWhere('counterpart_id', $user->id)
            ->with('milestones')->latest()->get();

        return view('trust-profile', compact('user', 'trustScore', 'threshold', 'exchanges'));
    }
}