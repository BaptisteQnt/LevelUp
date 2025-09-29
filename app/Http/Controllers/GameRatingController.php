<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameRating;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GameRatingController extends Controller
{
    public function store(Request $request, Game $game): RedirectResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        GameRating::updateOrCreate(
            [
                'game_id' => $game->id,
                'user_id' => $request->user()->id,
            ],
            [
                'rating' => $validated['rating'],
            ]
        );

        return back()->with('success', 'Votre note a été enregistrée.');
    }
}
