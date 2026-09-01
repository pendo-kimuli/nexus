<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    protected $fillable = [
        'initiator_id', 'counterpart_id', 'value_declaration_id', 'title', 'contract_terms', 'status',
    ];

    public function initiator() { return $this->belongsTo(User::class, 'initiator_id'); }
    public function counterpart() { return $this->belongsTo(User::class, 'counterpart_id'); }
    public function valueDeclaration() { return $this->belongsTo(ValueDeclaration::class); }
    public function milestones() { return $this->hasMany(Milestone::class); }
}