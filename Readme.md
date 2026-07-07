# 🐔 Chicken Farm — Ferme de poules

Vincent GASTON-CARRÈRE 4 IW 2
Application web de gestion et de vente pour une ferme avicole : élevage de poules, suivi de production, vente d'œufs et de viande, gestion des enclos et couveuses.

---

## 🚀 Démarrage rapide (Docker)

### Prérequis

- [Docker](https://docs.docker.com/get-docker/) + [Docker Compose](https://docs.docker.com/compose/install/)
- [Make](https://www.gnu.org/software/make/) (optionnel, ou lisez le Makefile pour les commandes équivalentes)

### Installation

```bash
# 1. Cloner le projet
git clone <url-du-repo> chicken-farm
cd chicken-farm

# 2. Premier lancement (build + démarrage)
make install

# 3. Initialiser la base de données (migrations + fixtures)
make db
```

### Lancement quotidien

```bash
make up        # Démarrer les services
make down      # Arrêter les services
make restart   # Redémarrer
```

### Accès aux services

| Service | URL | Description |
|---------|-----|-------------|
| 🌐 **Application** | http://localhost:8089 | L'app Symfony |
| 🗄️ **Adminer** | http://localhost:8088 | Interface d'administration de la BDD |
| 📧 **Mailpit** | http://localhost:8025 | Capture des emails (dev) |
| 🐘 **PostgreSQL** | `localhost:5439` | Base de données |

---

## 🔧 Commandes utiles

```bash
make sh         # Shell dans le conteneur PHP
make cache      # Vider le cache Symfony
make migrate    # Exécuter les migrations Doctrine
make fixtures   # Charger les fixtures (comptes de test + données)
make db         # Migrations + fixtures en une commande
make logs       # Voir les logs PHP
make test       # Lancer les tests PHPUnit
make ci         # Tout vérifier (lint + phpstan + tests)
```

### Charger les fixtures (seed la base)

```bash
make fixtures
```

Cette commande exécute `doctrine:fixtures:load` qui va :

1. **Purger** la base de données
2. **Charger** `AppFixtures` avec les données suivantes :

### 👤 Comptes de test (fixtures)

| Rôle | Email | Mot de passe |
|------|-------|-------------|
| 🛡️ **Admin** | `admin@ferme.fr` | `admin123` |
| 👨‍🌾 **Manager** | `manager@ferme.fr` | `manager123` |
| 👤 **Client** | `paul@test.fr` | `client123` |
| 👤 **Client** | `marie@test.fr` | `client123` |

Les fixtures créent aussi des produits, stocks, enclos, poules, coqs, etc.

---

## 📖 Pages disponibles

### Front (visiteurs et clients)

| Page | URL | Description |
|------|-----|-------------|
| Accueil | `/` | Page d'accueil de la ferme |
| Catalogue | `/catalogue` | Liste des produits à vendre |
| Détail produit | `/catalogue/{id}` | Fiche détaillée d'un produit |
| Panier | `/panier` | Mon panier d'achat |
| Commandes | `/commande` | Historique de mes commandes |
| Connexion | `/login` | Page de connexion |
| Inscription | `/register` | Créer un compte client |

### Back-office (admin/manager)

| Page | URL | Description |
|------|-----|-------------|
| Dashboard | `/admin` | Tableau de bord avec indicateurs |
| Produits | `/admin/produits` | Gestion des produits |
| Commandes | `/admin/commandes` | Gestion des commandes |
| Stocks | `/admin/stocks` | Suivi des stocks |
| Utilisateurs | `/admin/utilisateurs` | Gestion des utilisateurs |
| Enclos | `/admin/ferme/enclos` | Gestion des enclos |
| Couveuses | `/admin/ferme/couveuses` | Gestion des couveuses |
| Journal | `/admin/ferme/journal` | Journal d'activités de la ferme |

### API REST

Documentation interactive disponible sur **http://localhost:8089/api/doc** (Swagger UI).

| Méthode | Endpoint | Auth | Description |
|---------|----------|------|-------------|
| `GET` | `/api/v1/produits` | Public | Liste des produits |
| `GET` | `/api/v1/produits/{id}` | Public | Détail d'un produit |
| `POST` | `/api/v1/produits` | Admin | Créer un produit |
| `GET` | `/api/v1/commandes` | Connecté | Mes commandes |
| `GET` | `/api/v1/commandes/{id}` | Connecté | Détail commande |
| `GET` | `/api/v1/panier` | Connecté | Mon panier |
| `POST` | `/api/v1/panier/ajouter` | Connecté | Ajouter au panier |
| `GET` | `/api/v1/stocks` | Public | Liste des stocks |
| `GET` | `/api/v1/stocks/{id}` | Public | Détail d'un stock |

---

## 🏗️ Structure du projet

```
src/
  Entity/           # 16 entités Doctrine (Poule, Coq, Oeuf, Enclos, Produit...)
    Trait/           # UuidEntityTrait (UUID automatique)
  Repository/       # Repositories optimisés (pas de requêtes N+1)
  Controller/
    Front/           # Pages visiteurs (catalogue, panier, commandes, auth)
    Admin/           # Back-office (dashboard, produits, stocks, ferme)
    Api/V1/          # API REST documentée avec Swagger
  Form/             # Formulaires Symfony (ProduitType, RegistrationType...)
  Security/         # Voters (CommandeVoter, PanierVoter) + rôles
    Voter/
  Service/          # Logique métier (MailerService)
  Twig/             # Extensions Twig (ClassExtension)
  DataFixtures/     # AppFixtures (jeu de données de test)
templates/
  layout/           # Layouts de base (base, app, admin)
  front/            # Pages visiteurs
  admin/            # Pages back-office
  emails/           # Templates d'emails
config/             # Configuration Symfony
migrations/         # Doctrine migrations
tests/              # PHPUnit (unitaires + fonctionnels)
```

---

## 🧪 Tests

```bash
make test             # Tous les tests
make test-functional  # Tests fonctionnels uniquement
make ci               # Tout : lint conteneur + lint Twig + PHPStan + tests
```

---

## 📦 Modèle de données (16 entités)

### Volaille et ferme 🐔

- **Gallinace** → Poule, Coq, Poussin (héritage)
- **Enclos** regroupe les gallinacés
- **Poule** → pond des **Oeufs**
- **Oeuf** → peut devenir une **Incubation** en **Couveuse**
- **Incubation** → **Naissance** → **Poussin**
- **Nourriture** + **GallinaceNourriture** (rations)
- **JournalFerme** : suivi quotidien

### E-commerce 🛒

- **Produit** (œufs, viande, volailles) + **Stock**
- **Panier** → **Commande** → **CommandeDetail** + **Livraison**
- **Viande** : vente au poids
- **Ticket** : support client

### Utilisateurs 👤

- **User** (STI) → **Client**, **Manager**, **Admin**
- **Client** : panier, commandes
- **Manager** : gestion ferme (enclos, couveuses, stocks)
- **Admin** : accès complet

---

## ⚙️ Stack technique

| Composant | Version |
|-----------|---------|
| PHP | 8.4 |
| Symfony | 8.0 |
| Doctrine ORM | 3 + Migrations |
| PostgreSQL | 16 |
| Front | Twig, Asset Mapper, Stimulus, UX Turbo |
| CSS | TailwindCSS |
| API | NelmioApiDocBundle (Swagger UI) |
| Files d'attente | Symfony Messenger |
| Email dev | Mailpit |
| Tests | PHPUnit 12 |
