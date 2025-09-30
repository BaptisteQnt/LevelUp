# Authentification API avec JSON Web Token (JWT)

Ce projet Laravel peut exposer des endpoints d'API protégés via des JSON Web Tokens (JWT). Le package le plus simple à intégrer à Laravel 12 est [`php-open-source-saver/jwt-auth`](https://github.com/PHP-Open-Source-Saver/jwt-auth), un fork maintenu de `tymon/jwt-auth`.

## Installation du package

```bash
composer require php-open-source-saver/jwt-auth
```

Une fois le package installé, publiez la configuration :

```bash
php artisan vendor:publish --provider="PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider"
```

Cela crée le fichier `config/jwt.php`. Générez ensuite une clé secrète qui sera stockée dans le `.env` :

```bash
php artisan jwt:secret
```

> Cette commande ajoute `JWT_SECRET=...` à votre fichier `.env`. Pensez à synchroniser cette variable sur tous vos environnements (local, staging, production).

## Configuration Laravel

1. **Alias et provider** – Si vous utilisez l'auto-discovery de Laravel 12, aucun enregistrement manuel n'est nécessaire. Sinon, ajoutez dans `config/app.php` :
   ```php
   'providers' => [
       // ...
       PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider::class,
   ],

   'aliases' => [
       // ...
       'JWTAuth' => PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth::class,
       'JWTFactory' => PHPOpenSourceSaver\JWTAuth\Facades\JWTFactory::class,
   ],
   ```

2. **Middleware** – Dans `app/Http/Kernel.php`, enregistrez les middleware JWT pour protéger vos routes API :
   ```php
   protected $routeMiddleware = [
       // ...
       'jwt.verify' => \App\Http\Middleware\JwtMiddleware::class,
   ];
   ```
   Créez ensuite le middleware correspondant pour valider le token ou utilisez directement les middleware fournis par le package (`auth:api` avec le guard `jwt`).

3. **Guard API** – Dans `config/auth.php`, configurez le guard `api` pour utiliser le driver `jwt` :
   ```php
   'guards' => [
       'api' => [
           'driver' => 'jwt',
           'provider' => 'users',
       ],
   ],
   ```

## Utilisation de base

- Pour émettre un token, authentifiez l'utilisateur via les credentials classiques :
  ```php
  if (! $token = auth()->attempt($credentials)) {
      return response()->json(['error' => 'Unauthorized'], 401);
  }

  return response()->json([
      'access_token' => $token,
      'token_type' => 'Bearer',
      'expires_in' => auth()->factory()->getTTL() * 60,
  ]);
  ```

- Pour protéger vos routes d'API, placez-les derrière le middleware `auth:api` dans `routes/api.php` :
  ```php
  Route::middleware('auth:api')->get('/user', function (Request $request) {
      return $request->user();
  });
  ```

- Le front-end (Vue/Inertia) devra envoyer le header `Authorization: Bearer <token>` sur les requêtes protégées.

## Tests rapides

Après configuration, vous pouvez vérifier que tout fonctionne avec une simple suite :

```bash
php artisan test --testsuite=Feature --filter=Auth
```

Ajoutez vos propres tests d'API pour couvrir l'émission et la révocation des tokens.

---

Pour aller plus loin :
- Documentation officielle du package : <https://jwt-auth.readthedocs.io>
- Spécification JWT (RFC 7519) : <https://datatracker.ietf.org/doc/html/rfc7519>
