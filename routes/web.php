<?php

use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\GameController;
use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\Admin\PowersController;
use App\Http\Controllers\GameRatingController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TipController;
use Laravel\Cashier\Http\Controllers\WebhookController;
use Illuminate\Http\Request;


$dashboardPage = function (Request $request) {
    $user = $request->user();

    if ($user) {
        $user->refresh();
    }

    $subscription = $user?->subscription('default');

    return Inertia::render('Dashboard', [
        'isSubscribed'  => $subscription?->active() ?? false,
        'onGracePeriod' => $subscription?->onGracePeriod() ?? false,
        'endsAt'        => optional($subscription?->ends_at)->format('d/m/Y'),
    ]);
};

Route::get('/', $dashboardPage)->name('home');

Route::middleware(['auth', 'verified'])->get('/dashboard', $dashboardPage)->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/games', [GameController::class, 'index'])->name('games.index');
    Route::get('/games/{slug}', [GameController::class, 'show'])->name('games.show');
    Route::post('/games/{game}/rating', [GameRatingController::class, 'store'])->name('games.rating.store');
});
Route::middleware('auth')->delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
Route::middleware('auth')->post('/tips', [TipController::class, 'store'])->name('tips.store');
Route::middleware('auth')->delete('/tips/{tip}', [TipController::class, 'destroy'])->name('tips.destroy');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/moderation', ModerationController::class)->name('admin.moderation.index');
    Route::get('/admin/powers', [PowersController::class, 'index'])->name('admin.powers.index');
    Route::patch('/admin/powers/{user}', [PowersController::class, 'update'])->name('admin.powers.update');
    Route::patch('/admin/comments/{comment}/approve', [CommentController::class, 'approve'])->name('admin.comments.approve');
    Route::patch('/admin/tips/{tip}/approve', [TipController::class, 'approve'])->name('admin.tips.approve');
});

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

Route::get('/information', fn () => Inertia::render('Information'))->name('information');
Route::get('/presentation', fn () => Inertia::render('Presentation'))->name('presentation');


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
