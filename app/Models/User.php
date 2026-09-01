<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone_number', 'role', 'is_active', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function valueDeclarations() { return $this->hasMany(ValueDeclaration::class); }
    public function trustScore() { return $this->hasOne(TrustScore::class); }
    public function exchangesAsInitiator() { return $this->hasMany(Exchange::class, 'initiator_id'); }
    public function exchangesAsCounterpart() { return $this->hasMany(Exchange::class, 'counterpart_id'); }
    public function capitalAccessApplications() { return $this->hasMany(CapitalAccess::class); }

    public function isIndividual(): bool { return $this->role === 'individual'; }
    public function isInvestor(): bool { return $this->role === 'investor'; }
    public function isAdmin(): bool { return $this->role === 'admin'; }
}