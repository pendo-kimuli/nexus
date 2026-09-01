<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapitalAccess extends Model
{
    protected $table = 'capital_access';
    protected $fillable = ['user_id', 'amount_requested', 'purpose', 'status', 'daraja_transaction_id', 'disbursed_at'];

    protected function casts(): array
    {
        return ['disbursed_at' => 'datetime'];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function interests() { return $this->hasMany(CapitalAccessInterest::class); }
}