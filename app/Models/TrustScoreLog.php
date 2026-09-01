<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrustScoreLog extends Model
{
    protected $fillable = ['user_id', 'score', 'reason'];
    public $timestamps = false;

    public function user() { return $this->belongsTo(User::class); }
}