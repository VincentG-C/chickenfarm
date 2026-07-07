# AGENTS.md — Chicken Farm (Ferme de poules)

Instructions pour les agents IA travaillant sur ce dépôt. Répondre en **français**. Ne pas ajouter de commentaires dans le code PHP/Twig/JS sauf nécessité métier non évidente.

## Contexte projet

Application web de gestion et de vente pour une ferme avicole. Référence fonctionnelle : `Cahier des charges.md`. Schéma des entités : `entitesUML.txt`. Déployé sur Heroku.

### Utilisateurs et rôles

| Rôle | Classe | Capacités principales |
|------|--------|------------------------|
| **Client** | `Client` (étend `User`) | Catalogue, panier, commandes, historique |
| **Manager** | `Manager` (étend `User`) | Gestion ferme (enclos, couveuses, journal), stocks |
| **Admin** | `Admin` (étend `User`) | Accès complet : produits, commandes, utilisateurs, ferme |
| **Super Admin** | `Admin` avec `ROLE_SUPER_ADMIN` | Accès total + configuration |

Héritage : Single Table Inheritance sur `User` avec colonne discriminante `type` (`client`, `manager`, `admin`).

Rôles Symfony : `ROLE_USER`, `ROLE_MANAGER`, `ROLE_ADMIN`, `ROLE_SUPER_ADMIN` (hiérarchie dans `security.yaml`).

---

## Stack technique

| Composant | Version / choix |
|-----------|-----------------|
| PHP | **8.4** (`>=8.4` dans `composer.json`) |
| Symfony | **8.0.\*** |
| API | **NelmioApiDocBundle** (Swagger UI, pas API Platform) |
| ORM | Doctrine ORM 3 + Migrations |
| BDD | **PostgreSQL 16** |
| Front | Twig, Asset Mapper, Stimulus, UX Turbo |
| CSS | TailwindCSS |
| Files d'attente | Symfony Messenger (`doctrine://default`) |
| Email dev | Mailpit (SMTP 1025, UI 8025) |
| Tests | PHPUnit 12 |

---

## Environnement Docker

```bash
make install   # premier lancement (build + up)
make up        # démarrer les services
make down      # arrêter
make sh        # shell dans le conteneur PHP
make cache     # cache:clear
make logs      # logs PHP
```

| Service | URL / port |
|---------|------------|
| Application | http://localhost:8089 |
| Adminer (BDD) | http://localhost:8088 |
| Mailpit | http://localhost:8025 |
| PostgreSQL | `localhost:5439` |

**Toujours exécuter les commandes Symfony/Composer/PHPUnit dans le conteneur `php`** :

```bash
docker compose exec php php bin/console <commande>
docker compose exec php composer require <package>
docker compose exec php php bin/phpunit
```

Variables d'environnement clés : `DATABASE_URL`, `MESSENGER_TRANSPORT_DSN`, `MAILER_DSN`, `APP_SECRET`.

---

## Déploiement Heroku

```bash
git push heroku main
# Release command : migrations automatiques + cache clear
```

| Service | URL |
|---------|-----|
| App | https://chicken-farm-47b0bd212f2d.herokuapp.com |
| Swagger UI | https://chicken-farm-47b0bd212f2d.herokuapp.com/api/doc |
| Swagger JSON | https://chicken-farm-47b0bd212f2d.herokuapp.com/api/doc.json |

### Comptes de test (fixtures — charger avec `doctrine:fixtures:load`)

| Rôle | Email | Mot de passe |
|------|-------|-------------|
| Admin | `admin@ferme.fr` | `admin123` |
| Manager | `manager@ferme.fr` | `manager123` |
| Client | `paul@test.fr` | `client123` |
| Client | `marie@test.fr` | `client123` |

---

## Structure du code

```
src/
  Entity/           # Entités Doctrine (mapping attributs PHP 8)
    Trait/           # Traits réutilisables (UuidEntityTrait)
  Repository/       # Repositories Doctrine (requêtes DB)
  Controller/
    Front/           # Contrôleurs visiteurs (Home, Catalogue, Panier, Commande, Auth)
    Admin/           # Contrôleurs back-office (Ferme, Produit, Stock, Order, User)
    Api/V1/          # API REST (ApiController)
  Form/             # Form types Symfony (ProduitType, RegistrationType, AdminUserType)
  Security/         # Voters, Roles
    Voter/           # CommandeVoter, PanierVoter
  Service/          # Logique métier (MailerService)
  Twig/             # Extensions Twig (ClassExtension)
  DataFixtures/     # AppFixtures (jeu de données de test)
config/
  packages/         # Config bundles
  routes/           # Routes additionnelles
templates/
  layout/           # Layouts de base (base.html.twig, app.html.twig, admin.html.twig)
  front/            # Pages visiteurs
  admin/            # Pages back-office
  emails/           # Templates d'emails
assets/             # JS/CSS via Asset Mapper
tests/              # PHPUnit
  Unit/
  Functional/
migrations/         # Doctrine migrations
```

Namespace racine : `App\`. Autoload PSR-4 dans `composer.json`.

---

## Modèle de données (16 entités)

### Volaille et ferme

| Entité | Rôle | Relations clés |
|--------|------|----------------|
| `Gallinace` | Superclasse volaille (Mapped Superclass ou héritage) | ManyToOne → `Enclos` |
| `Poule` | Poule pondeuse | Hérite `Gallinace`, OneToMany → `Oeuf` |
| `Coq` | Coq reproducteur | Hérite `Gallinace` |
| `Poussin` | Jeune volaille | Hérite `Gallinace`, ManyToOne → `Naissance` |
| `Enclos` | Enclos/poulailler | OneToMany → `Gallinace` |
| `Oeuf` | Œuf pondu | ManyToOne → `Poule`, peut devenir `Incubation` |
| `Couveuse` | Couveuse artificielle | OneToMany → `Incubation` |
| `Incubation` | Période d'incubation d'un œuf | ManyToOne → `Oeuf` + `Couveuse` |
| `Naissance` | Événement de naissance | OneToMany → `Poussin` |
| `Nourriture` | Type d'aliment | ManyToMany → `Gallinace` (via `GallinaceNourriture`) |
| `GallinaceNourriture` | Table pivot (ration) | ManyToOne → `Gallinace` + `Nourriture` |
| `JournalFerme` | Journal d'activités quotidiennes | Champ `categorie` + `description` |

### E-commerce

| Entité | Rôle | Relations clés |
|--------|------|----------------|
| `Produit` | Produit vendu (œufs, viande, etc.) | OneToOne → `Stock` |
| `Stock` | Stock d'un produit | OneToOne → `Produit` |
| `Commande` | Commande client | ManyToOne → `Client`, OneToMany → `CommandeDetail` |
| `CommandeDetail` | Ligne de commande | ManyToOne → `Commande` + `Produit` |
| `Panier` | Panier d'achat | OneToOne → `Client`, OneToMany → `PanierProduit` |
| `PanierProduit` | Ligne de panier | ManyToOne → `Panier` + `Produit` |
| `Livraison` | Informations de livraison | OneToOne → `Commande` |
| `Viande` | Produit carné (héritage `Produit` ou champ `type`) | Vente au poids |
| `Ticket` | Ticket de support | ManyToOne → `User` |
| `Client` | Utilisateur client | Étend `User`, OneToMany → `Commande`, OneToOne → `Panier` |
| `Manager` | Utilisateur manager | Étend `User` |
| `Admin` | Utilisateur admin | Étend `User` |
| `User` | Entité de base (STI) | `type` : client, manager, admin |

### Trait

`UuidEntityTrait` : fournit un identifiant UUID automatique via `$id` + constructeur.

---

## Bonnes pratiques Symfony (obligatoires)

### Architecture

- **Contrôleurs fins** : pas de logique métier ; déléguer aux services injectés.
- **Services stateless** : une responsabilité par service.
- **Entités = modèle persistence** : pas de dépendance HTTP.
- **Repository** : requêtes Doctrine uniquement ; pas de logique métier lourde. Utiliser `createBaseQueryBuilder()` avec `LEFT JOIN` pour les relations fréquentes.
- **Messenger** : emails, notifications → messages async.
- **Serialization groups** : utiliser des groupes (`api:produit:read`, `api:commande:write`, etc.) pour contrôler l'exposition API.

### Configuration et DI

- Paramètres applicatifs dans `config/services.yaml` (`parameters:`) ou variables d'env — **jamais de secrets en dur**.
- Services auto-enregistrés via `App\:` + `autowire` / `autoconfigure`.
- Préférer les **attributs PHP** (`#[Route]`, `#[IsGranted]`, `#[ORM\...]`) aux YAML.

### Doctrine

- Migrations pour tout changement de schéma : `doctrine:migrations:diff` puis `migrate`.
- Relations explicites (`inversedBy` / `mappedBy`), `cascade` et `orphanRemoval` seulement si justifiés.
- Types : `DateTimeImmutable` pour les dates métier ; UUID pour les identifiants (via `UuidEntityTrait`).
- Index sur colonnes recherchées (email, statut commande, date).

### Sécurité

- Hacher les mots de passe via `UserPasswordHasherInterface`.
- **CSRF** activé sur les formulaires web.
- **Voters** pour autorisation fine (propriétaire commande/panier).
- API : session Symfony (cookie), pas de JWT pour l'instant.
- Valider et assainir toutes les entrées (`Validator`, types stricts PHP).
- `access_control` et `#[IsGranted]` cohérents avec les rôles métier.

### API (NelmioApiDocBundle)

- Documenter avec `#[OA\Get]`, `#[OA\Post]`, `#[OA\Response]`, `#[OA\RequestBody]`.
- Utiliser `Nelmio\ApiDocBundle\Attribute\Model` pour référencer les entités.
- Sérialization groups Symfony (`@Groups`) pour limiter les champs exposés.
- Routes préfixées `/api/v1`.
- Opérations sensibles protégées par `#[IsGranted]`.

### Formulaires et validation

- Constraints sur entités/DTO (`#[Assert\...]`).
- `FormType` dédiés ; pas de `$request->request->get()` brut.

### Twig et front

- Deux layouts : `app.html.twig` (front client), `admin.html.twig` (back-office).
- Pages front : home, catalogue, panier, commandes, auth (login/register).
- Pages admin : dashboard, produits, stocks, commandes, utilisateurs, ferme (enclos, couveuses, journal).
- Assets via **Asset Mapper**, Stimulus pour le JS interactif.
- Sidebar admin dans `layout/partials/admin/sidebar.html.twig`.

### Performance et qualité

- Requêtes N+1 : `JOIN` / `addSelect` dans les repositories (via `createBaseQueryBuilder()`).
- Cache Symfony pour config/routes en prod.
- Logs via Monolog ; niveaux adaptés.
- Tests fonctionnels pour parcours critiques (auth, commande) ; PHPUnit dans `tests/`.

### Conventions de code

- `declare(strict_types=1);` en tête des fichiers PHP.
- Types de retour et paramètres typés ; propriétés `private` + getters/setters.
- Nommage anglais pour le code (classes, méthodes), français acceptable pour labels UI et messages utilisateur.
- Serialization groups : préfixe `api:` suivi du nom de l'entité en minuscule et de l'opération (`read`, `write`).
- Pas de commit de `.env`, secrets, ou `var/` / `vendor/`.

---

## API REST — Endpoints

| Méthode | Path | Auth | Description |
|---------|------|------|-------------|
| GET | `/api/v1/produits` | Public | Liste des produits |
| GET | `/api/v1/produits/{id}` | Public | Détail produit |
| POST | `/api/v1/produits` | Admin | Créer un produit |
| GET | `/api/v1/commandes` | Connecté | Mes commandes (admin = toutes) |
| GET | `/api/v1/commandes/{id}` | Connecté | Détail commande |
| GET | `/api/v1/panier` | Connecté | Mon panier |
| POST | `/api/v1/panier/ajouter` | Connecté | Ajouter au panier |
| GET | `/api/v1/stocks` | Public | Liste stocks |
| GET | `/api/v1/stocks/{id}` | Public | Détail stock |

Documentation interactive : `/api/doc` (Swagger UI).

---

## Domaine métier (entités principales)

### Volaille 🐔
- `Poule` pond des `Oeuf` — suivi par date, qualité, quantité
- `Coq` — reproduction avec `Poule` → `Oeuf` fécondé → `Incubation` en `Couveuse` → `Naissance` → `Poussin`
- `Enclos` regroupe les `Gallinace` par typologie/densité
- `Nourriture` + `GallinaceNourriture` pour tracer la consommation

### E-commerce 🛒
- `Produit` (œufs, viande, volailles vivantes) avec `Stock`
- `Panier` → `Commande` → `CommandeDetail` + `Livraison`
- `Client` passe commande, `Manager`/`Admin` gère les stocks

### Back-office 📊
- Dashboard avec indicateurs (stocks bas, nouvelles commandes)
- Gestion des `Produit` + `Stock`
- Gestion des `Commande` (statuts : en_attente, confirmée, expédiée, livrée, annulée)
- Gestion des `User` (CRUD, rôles)
- Gestion de la **Ferme** : `Enclos`/`Couveuse`/`JournalFerme`

---

## Git et livrables

- Ne créer de **commit** ou **push** que sur demande explicite de l'utilisateur.
- Messages de commit courts, orientés « pourquoi ».
- Deux remotes : `origin` (GitHub), `heroku` (déploiement).

---

## Pièges connus

- Single Table Inheritance sur `User` : la colonne `type` doit être bien settée (client/manager/admin).
- Les annotations `#[OA\Info]`, `#[OA\Server]`, `#[OA\SecurityScheme]` ne doivent figurer que dans `config/packages/nelmio_api_doc.yaml` — pas sur les classes contrôleur.
- `createBaseQueryBuilder()` dans les repositories doit inclure les `LEFT JOIN` essentiels.
- Les fixtures (`AppFixtures`) écrasent la base à chaque chargement.
- Le `Procfile` Heroku exécute une release command (migrations + cache:clear) à chaque déploiement.
