<?php

namespace App\Http\Controllers;

use App\Models\Tip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TipController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'game_id' => 'required|exists:games,id',
            'content' => 'required|string|min:3|max:1000',
        ]);

        Tip::create([
            'user_id' => Auth::id(),
            'game_id' => $validated['game_id'],
            'content' => $validated['content'],
        ]);

        return back()->with('success', 'Astuce ajoutée.');
    }

    public function destroy(Request $request, Tip $tip)
    {
        $user = $request->user();

        if (! $user || ($tip->user_id !== $user->id && ! $user->is_admin)) {
            abort(403);
        }

        $tip->delete();

        return back()->with('success', 'Astuce supprimée.');
    }
}
