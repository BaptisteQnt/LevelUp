# Guide de déploiement

## Sécuriser le transport TLS

Activez TLS sur l'environnement de production (HTTPS avec un certificat valide) avant d'activer la redirection publique. Les en-têtes HSTS et la politique de sécurité du contenu fournis par `App\\Http\\Middleware\\SecurityHeaders` ne sont pris en compte que sur des connexions sécurisées : vérifiez donc que le certificat et la chaîne intermédiaire sont correctement installés et que les terminaux acceptent les suites TLS récentes.
