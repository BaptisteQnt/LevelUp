<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    // Page avec les plans
    public function plans()
    {
        return inertia('billing/Plans', [
            'stripeKey' => config('cashier.key'),
            'prices' => [
                ['id' => env('STRIPE_PRICE_DEFAULT'), 'name' => 'Abonnement mensuel', 'amount' => '9,99 € / mois'],
            ],
        ]);
    }

    // Démarrer le checkout Stripe (Checkout hébergé)
    public function checkout(Request $request)
    {
        $price = $request->query('price') ?? env('STRIPE_PRICE_DEFAULT');
        abort_unless($price, 400, 'Missing price');

        $checkout = $request->user()
            ->newSubscription('default', $price)
            ->allowPromotionCodes()
            ->checkout([
                'success_url' => route('billing.success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => route('billing.cancel'),
            ]);

        $redirectResponse = $checkout->redirect();

        if ($request->inertia()) {
            return Inertia::location($redirectResponse->getTargetUrl());
        }

        return $redirectResponse;
    }

    // Page succès
    public function success(Request $request)
    {
        // Optionnel : vérifier la session_id, afficher un message, etc.
        return inertia('billing/Success');
    }

    // Page annulation
    public function cancel()
    {
        return inertia('billing/Cancel');
    }

    // Portail de facturation Stripe (self-service)
    public function portal(Request $request)
    {
        return $request->user()->redirectToBillingPortal(route('billing.plans'));
    }
}
