<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Inertia\Inertia;
use Inertia\Response;
class GameController extends Controller
{
    public function index(): Response
    {
        $lang = request('lang', 'en');

        $games = Game::orderByDesc('created_at')
            ->get(['id', 'title', 'slug', 'cover_url', 'summary', 'storyline', 'description'])
            ->map(function (Game $game) use ($lang) {
                $texts = $game->localizedTexts($lang);
                $body = collect([
                    $texts['storyline'] ?? null,
                    $texts['summary'] ?? null,
                ])->filter()->implode("\n\n");

                return [
                    'id'        => $game->id,
                    'title'     => $game->title,
                    'slug'      => $game->slug,
                    'cover_url' => $game->cover_url,
                    'summary'   => $texts['summary'],
                    'storyline' => $texts['storyline'],
                    'description' => $body !== '' ? $body : null,
                ];
            });

        return Inertia::render('games/Index', [
            'games' => $games,
        ]);
    }

    public function show(string $slug): \Inertia\Response
    {
        $game = Game::where('slug', $slug)->firstOrFail();
        $lang = request('lang', 'en');

        $texts = $game->localizedTexts($lang);
        $body = collect([
            $texts['storyline'] ?? null,
            $texts['summary'] ?? null,
        ])->filter()->implode("\n\n");

        return Inertia::render('games/Show', [
            'game' => [
                'id'          => $game->id,
                'title'       => $game->title,
                'cover_url'   => $game->cover_url,
                'summary'     => $texts['summary'],
                'storyline'   => $texts['storyline'],
                'description' => $body !== '' ? $body : null,
                'comments'    => $game->comments()
                                    ->with('user:id,username')
                                    ->latest()
                                    ->get(),
            ],
            'flash' => session('success'),
        ]);
    }


}
