<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrustScore extends Model
{
    protected $fillable = [
        'user_id', 'score', 'timeliness_score', 'rating_score', 'completeness_score', 'dispute_score', 'capital_eligible',
    ];

    protected function casts(): array
    {
        return ['capital_eligible' => 'boolean'];
    }

    public function user() { return $this->belongsTo(User::class); }
}