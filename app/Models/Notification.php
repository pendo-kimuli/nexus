<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'nexus_notifications';
    protected $fillable = ['user_id', 'message', 'channel', 'status'];

    public function user() { return $this->belongsTo(User::class); }
}