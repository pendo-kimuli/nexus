<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValueDeclaration extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'title',
        'description',
        'skills_offered',
        'skills_sought',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Turns "graphic design, photoshop, branding" into ['graphic design','photoshop','branding']
    private function keywords(string $text): array
    {
        return array_filter(array_map('trim', explode(',', strtolower($text))));
    }

    // True if this declaration's "offered" skills overlap with the other's "sought" skills, or vice versa
    public function matchesWith(ValueDeclaration $other): bool
    {
        $myOffered = $this->keywords($this->skills_offered);
        $mySought = $this->keywords($this->skills_sought);
        $theirOffered = $other->keywords($other->skills_offered ?? '');
        $theirSought = $other->keywords($other->skills_sought ?? '');

        return count(array_intersect($myOffered, $theirSought)) > 0
            || count(array_intersect($mySought, $theirOffered)) > 0;
    }
}