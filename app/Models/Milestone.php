<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    protected $fillable = [
        'exchange_id', 'title', 'description', 'due_date',
        'initiator_confirmed_at', 'counterpart_confirmed_at',
        'initiator_rating', 'counterpart_rating', 'status', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'initiator_confirmed_at' => 'datetime',
            'counterpart_confirmed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function exchange() { return $this->belongsTo(Exchange::class); }
}