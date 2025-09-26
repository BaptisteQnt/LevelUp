<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
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

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== auth()->id()) {
            abort(403);
        }

        $comment->delete();

        return back()->with('success', 'Commentaire supprimé.');
    }

}

