<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserProfileController extends Controller
{
    public function show(string $username): Response
    {
        $user = User::where('username', $username)->firstOrFail();

        return Inertia::render('users/Show', [
            'user' => [
                'name' => $user->name,
                'username' => $user->username,
                'city' => $user->city,
                'country' => $user->country,
                'age' => $user->age,
                'created_at' => $user->created_at->diffForHumans(),
                // 'avatar' => $user->avatar, // plus tard
            ],
        ]);
    }
}
