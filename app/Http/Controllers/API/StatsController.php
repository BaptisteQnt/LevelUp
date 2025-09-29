<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Game;
use App\Models\GameRating;
use App\Models\Tip;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    public function index(): JsonResponse
    {
        $ratingsCount = GameRating::count();

        $ratingsAverage = null;

        if ($ratingsCount > 0) {
            $ratingsAverage = round((float) GameRating::avg('rating'), 1);
        }

        return response()->json([
            'games' => [
                'total' => Game::count(),
                'rated_total' => GameRating::query()->distinct()->count('game_id'),
            ],
            'ratings' => [
                'total' => $ratingsCount,
                'average' => $ratingsAverage,
            ],
            'comments' => [
                'approved_total' => Comment::approved()->count(),
            ],
            'tips' => [
                'approved_total' => Tip::approved()->count(),
            ],
            'users' => [
                'total' => User::count(),
            ],
        ]);
    }

    public function gameRating(Game $game): JsonResponse
    {
        $ratingsCount = $game->ratings()->count();

        $averageRating = null;

        if ($ratingsCount > 0) {
            $averageRating = round((float) $game->ratings()->avg('rating'), 1);
        }

        return response()->json([
            'game_id' => $game->id,
            'average_rating' => $averageRating,
            'ratings_count' => $ratingsCount,
        ]);
    }
}
