<?php

use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\GameController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\SubscriptionController;
use Laravel\Cashier\Http\Controllers\WebhookController;
use Illuminate\Http\Request;


Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->get('/dashboard', function (Request $request) {
    $user = $request->user()->refresh();
    $subscription = $user->subscription('default');

    return Inertia::render('Dashboard', [
        'isSubscribed'  => $subscription?->active() ?? false,
        'onGracePeriod' => $subscription?->onGracePeriod() ?? false,
        'endsAt'        => optional($subscription?->ends_at)->format('d/m/Y'),
    ]);
})->name('dashboard');

Route::get('/games', [GameController::class, 'index'])->name('games.index');
Route::get('/games/{slug}', [GameController::class, 'show'])->name('games.show');
Route::middleware('auth')->delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

use App\Http\Controllers\Auth\SocialiteController;

Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
    ->whereIn('provider', ['google','discord'])
    ->name('oauth.redirect');

Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])
    ->whereIn('provider', ['google','discord'])
    ->name('oauth.callback');



Route::get('/users/{username}', [UserProfileController::class, 'show'])->name('users.show');

Route::middleware('auth')->post('/comments', [CommentController::class, 'store'])->name('comments.store');

// Pages plans/checkout/portal protégées pour utilisateurs connectés
Route::middleware(['auth','verified'])->group(function () {
    Route::get('/billing/plans', [SubscriptionController::class, 'plans'])->name('billing.plans');
    Route::get('/billing/checkout', [SubscriptionController::class, 'checkout'])->name('billing.checkout'); // ⬅️ GET
    Route::get('/billing/success', [SubscriptionController::class, 'success'])->name('billing.success');
    Route::get('/billing/cancel', [SubscriptionController::class, 'cancel'])->name('billing.cancel');
    Route::get('/billing/portal', [SubscriptionController::class, 'portal'])->name('billing.portal');
});

// Webhook Stripe (Cashier le gère)
Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook']);

Route::middleware(['auth','verified','subscribed'])
    ->get('/premium', fn() => Inertia::render('Premium/Index'));


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
