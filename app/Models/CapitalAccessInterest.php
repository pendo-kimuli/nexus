<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapitalAccessInterest extends Model
{
    protected $fillable = ['capital_access_id', 'investor_id'];

    public function capitalAccess() { return $this->belongsTo(CapitalAccess::class); }
    public function investor() { return $this->belongsTo(User::class, 'investor_id'); }
}