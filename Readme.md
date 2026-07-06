# 🐔 Ferme de Poule — Application Symfony

Application web de gestion de ferme avicole : élevage de poules, vente d'œufs, viandes, tickets de visite, et suivi de la production.

---

## 🚀 Déploiement sur Heroku

### Prérequis

- Compte [Heroku](https://heroku.com)
- [Heroku CLI](https://devcenter.heroku.com/articles/heroku-cli) installé

### Étapes

```bash
# 1. Créer l'application Heroku
heroku create ferme-de-poule --region europe

# 2. Ajouter PostgreSQL (gratuit)
heroku addons:create heroku-postgresql:mini

# 3. Configurer les variables d'environnement
heroku config:set APP_ENV=prod
heroku config:set APP_SECRET=$(openssl rand -hex 32)
# heroku config:set APP_SECRET=$(dd if=/dev/urandom bs=32 count=1 2>/dev/null | xxd -ps -c 64)  # sur Linux
heroku config:set MAILER_DSN=null://null
heroku config:set MESSENGER_TRANSPORT_DSN=doctrine://default

# 4. Pousser le code
git push heroku main

# 5. (Optionnel) Charger les fixtures en prod
heroku run "php bin/console doctrine:fixtures:load --no-interaction"
```

### URL

L'application sera accessible à : `https://nom-de-votre-app.herokuapp.com`

### Mise à jour

```bash
git push heroku main
# Les migrations s'exécutent automatiquement (release phase dans le Procfile)
```

---

## ⚙️ Développement local

### Avec Docker

```bash
make install   # Premier lancement
make up        # Démarrer
make sh        # Shell PHP
make cache     # Cache clear
```

| Service | URL |
|---------|-----|
| Application | http://localhost:8089 |
| Adminer (BDD) | http://localhost:8088 |
| Mailpit | http://localhost:8025 |

### Accès

| Rôle | Email | Mot de passe |
|------|-------|-------------|
| Admin | admin@ferme.fr | admin123 |
| Manager | manager@ferme.fr | manager123 |
| Client | paul@test.fr | client123 |

---

## 🧪 Qualité

```bash
make phpstan      # Analyse statique (niveau 5)
make lint-twig    # Validation Twig
make test         # Tests PHPUnit
make ci           # Tout en un
```

---

## 🏗️ Structure du projet

```
src/
  Entity/        # Entités Doctrine (Poule, Coq, Oeuf, Enclos, Produit...)
  Repository/    # Repositories optimisés (pas de N+1)
  Controller/    # Contrôleurs fins
  Form/          # Formulaires Symfony
  Security/      # Voters, rôles
  Service/       # Logique métier (Mailer...)
  Twig/          # Extensions Twig
templates/       # Twig (admin + front)
config/          # Configuration Symfony
migrations/      # Doctrine migrations
```

---

## 📦 Fonctionnalités

- 🐓 **Gestion des gallinacés** : poules, coqs, poussins, enclos
- 🥚 **Production d'œufs** : suivi ponte, calibres, œufs fécondés
- 🏪 **Boutique en ligne** : œufs, viandes, tickets visite, nourriture
- 🛒 **Panier & commandes** avec suivi de livraison
- 📊 **Dashboard manager** : stocks, enclos, couveuses, journal de ferme
- 🔐 **Authentification** : admin, manager, client
- 🐣 **Couveuses & incubations** : suivi des naissances
