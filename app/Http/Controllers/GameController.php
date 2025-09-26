<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\GameTranslation;


class GameController extends Controller
{
    public function index(): Response
    {
        $games = Game::orderByDesc('created_at')->get(['id', 'title', 'slug', 'cover_url', 'description']);

        return Inertia::render('games/Index', [
            'games' => $games,
        ]);
    }

    public function show(string $slug): \Inertia\Response
    {
        $game = Game::where('slug', $slug)->firstOrFail();
        $lang = request('lang', 'en');

        return Inertia::render('games/Show', [
            'game' => [
                'id'          => $game->id,
                'title'       => $game->title,
                'cover_url'   => $game->cover_url,
                'description' => $game->translatedDescription($lang),
                'comments'    => $game->comments()
                                    ->with('user:id,username')
                                    ->latest()
                                    ->get(),
            ],
            'flash' => session('success'),
        ]);
    }


}
