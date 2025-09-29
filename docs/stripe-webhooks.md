# Webhooks Stripe pour l'abonnement Premium

Ce projet utilise [Laravel Cashier](https://laravel.com/docs/cashier) pour gérer les abonnements Stripe. Cashier expose automatiquement une route d'écoute et s'occupe de synchroniser les statuts de souscription lorsqu'il reçoit les webhooks Stripe. Voici comment la connecter et vérifier qu'un utilisateur est premium.

## 1. Route webhook déjà fournie

Le fichier [`routes/web.php`](../routes/web.php) enregistre la route `POST /stripe/webhook` vers le contrôleur fourni par Cashier :

```php
Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook']);
```

Le middleware CSRF exclut explicitement `stripe/*` ([`bootstrap/app.php`](../bootstrap/app.php)), ce qui permet à Stripe d'appeler l'URL sans token. Tant que cette route est exposée publiquement, Cashier va recevoir les événements Stripe et mettre à jour les modèles `User` via le trait `Billable`.

## 2. Configurer l'endpoint côté Stripe

1. Dans le dashboard Stripe, ouvrez **Developers > Webhooks**.
2. Ajoutez un endpoint pointant vers l'URL publique de votre application, par exemple `https://votre-domaine.test/stripe/webhook`.
3. Sélectionnez au minimum les événements liés aux abonnements : `checkout.session.completed`, `invoice.paid`, `invoice.payment_succeeded`, `customer.subscription.created`, `customer.subscription.updated`, `customer.subscription.deleted`.
4. Enregistrez la clé secrète de signature générée par Stripe et ajoutez-la à votre fichier `.env` :
   ```env
   STRIPE_WEBHOOK_SECRET="whsec_xxx"
   ```

Cashier lira automatiquement `STRIPE_WEBHOOK_SECRET` pour vérifier la signature.

> ℹ️ **Important :** Cashier traite les webhooks via la file d'attente Laravel. Assurez-vous qu'un worker tourne (`php artisan queue:work`) ou utilisez `QUEUE_CONNECTION=sync` en local si vous ne souhaitez pas gérer de worker. Sinon, les événements seront stockés en file d'attente et l'état de l'abonnement ne sera pas mis à jour.

## 3. Accorder l'accès Premium

Le modèle [`App\Models\User`](../app/Models/User.php) utilise le trait `Billable` de Cashier. Les champs comme `is_subscribed` ou le middleware `subscribed` se basent sur les données de souscription que Cashier synchronise.

En pratique :

- Lorsque Stripe envoie `checkout.session.completed` ou `invoice.payment_succeeded`, Cashier marque l'abonnement `default` de l'utilisateur comme actif.
- Si un paiement échoue, Stripe envoie `invoice.payment_failed` et Cashier place l'abonnement en période de grâce ou le résilie.
- Le middleware `subscribed` vérifie l'abonnement `default`, ce qui protège automatiquement les routes premium.

Si vous avez besoin d'une logique spécifique (par exemple envoyer un e-mail), créez votre propre contrôleur qui étend `Laravel\Cashier\Http\Controllers\WebhookController` et surchargez les méthodes `handleInvoicePaymentSucceeded` ou `handleCustomerSubscriptionUpdated`.

## 4. Tester en local

Installez le CLI Stripe et exécutez :

```bash
stripe listen --forward-to "http://127.0.0.1:8000/stripe/webhook"
```

Ensuite, lancez un paiement de test (`stripe checkout sessions create …`) ou simulez un événement :

```bash
stripe trigger invoice.payment_succeeded
```

Vérifiez dans vos logs Laravel (`storage/logs/laravel.log`) que l'événement est bien reçu. Vous pouvez également inspecter la colonne `stripe_status` de la table `subscriptions` pour confirmer que l'utilisateur est à jour.

## 5. Récupérer l'état premium côté application

- **Backend** : `Auth::user()->subscribed('default')` renvoie `true` si l'utilisateur est premium.
- **Frontend Inertia** : La route `/dashboard` expose `isSubscribed` pour afficher du contenu conditionnel.

Grâce à Cashier et aux webhooks, aucune synchronisation manuelle supplémentaire n'est nécessaire pour savoir si un utilisateur a payé l'offre premium.
