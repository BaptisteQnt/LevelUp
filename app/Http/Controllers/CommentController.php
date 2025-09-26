<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CommentController extends Controller
{
    public function index(Request $request): Response
    {
        $comments = Comment::query()
            ->with(['user:id,username', 'game:id,title,slug'])
            ->latest()
            ->paginate(15)
            ->through(fn (Comment $comment) => [
                'id' => $comment->id,
                'content' => $comment->content,
                'created_at' => $comment->created_at->toIso8601String(),
                'user' => [
                    'id' => $comment->user->id,
                    'username' => $comment->user->username,
                ],
                'game' => [
                    'id' => $comment->game->id,
                    'title' => $comment->game->title,
                    'slug' => $comment->game->slug,
                ],
            ]);

        return Inertia::render('admin/Comments', [
            'comments' => [
                'data' => $comments->items(),
                'links' => $comments->linkCollection()->map(fn ($link) => [
                    'url' => $link['url'],
                    'label' => $link['label'],
                    'active' => $link['active'],
                ])->values(),
                'meta' => [
                    'current_page' => $comments->currentPage(),
                    'last_page' => $comments->lastPage(),
                    'per_page' => $comments->perPage(),
                    'total' => $comments->total(),
                ],
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'game_id' => 'required|exists:games,id',
            'content' => 'required|string|min:3|max:1000',
        ]);

        Comment::create([
            'user_id' => Auth::id(),
            'game_id' => $validated['game_id'],
            'content' => $validated['content'],
        ]);

        return back()->with('success', 'Commentaire ajouté.');
    }

    public function destroy(Request $request, Comment $comment)
    {
        $user = $request->user();

        if (! $user || ($comment->user_id !== $user->id && ! $user->is_admin)) {
            abort(403);
        }

        $comment->delete();

        return back()->with('success', 'Commentaire supprimé.');
    }

}

