<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'twitch_id',
        'cover_url',
        'description',
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function translatedDescription(string $lang = 'en'): ?string
    {
        if ($lang !== 'fr') {
            return $this->description;
        }

        $tr = \App\Models\GameTranslation::where('game_id', $this->id)
            ->where('lang', 'fr')
            ->first();

        return $tr?->summary ?: $this->description;
    }

}


