# Cahier des Charges Fonctionnel
## Application de Gestion de Ferme Avicole

**Version :** 1.0  
**Date :** Juillet 2026  
**Auteur :** [Ton Nom / Groupe]  
**Technologie :** Symfony 7.x & Twig

---

## 1. Présentation du Projet

### 1.1 Contexte
La ferme avicole "Les Poulettes d'Or" souhaite digitaliser la gestion de son élevage et de sa vente directe. L'application doit permettre aux fermiers de suivre quotidiennement la production (œufs, viande, naissances) et aux clients d'acheter en ligne les produits de la ferme (œufs, viande, tickets de visite).

### 1.2 Objectifs
- **Centraliser** la gestion de l'élevage (suivi des poules, enclos, couveuses)
- **Simplifier** le suivi journalier de la production via un tableau de bord intuitif
- **Commercialiser** les produits de la ferme via une boutique en ligne
- **Offrir** une expérience client fluide (panier, commande, livraison)

### 1.3 Périmètre Fonctionnel

| Inclus | Exclus |
|--------|--------|
| Gestion des gallinacés (poules, coqs, poussins) | Gestion comptable avancée |
| Suivi de la production (ponte, incubation, naissances) | Module de paiement réel (simulation uniquement) |
| Boutique en ligne (panier, commande) | Gestion des employés |
| Gestion des stocks (œufs, viande, tickets) | Application mobile native |
| Espace administration | Interface en plusieurs langues |
| API de géolocalisation pour livraison | |

---

## 2. Rôles Utilisateurs

L'application distingue **trois rôles** avec des périmètres d'action clairement définis :

| Rôle | Description | Accès |
|------|-------------|-------|
| **ROLE_USER** | Client de la ferme | Peut consulter la boutique, gérer son panier, passer des commandes, consulter son historique |
| **ROLE_MANAGER** | Fermier / Gestionnaire d'élevage | Peut gérer l'élevage (suivi journalier, stocks), consulter les statistiques, gérer les commandes clients |
| **ROLE_ADMIN** | Administrateur système | Peut tout gérer (utilisateurs, rôles, configuration, paramètres) |

### 2.1 Matrice des Droits

| Fonctionnalité | ROLE_USER | ROLE_MANAGER | ROLE_ADMIN |
|----------------|-----------|--------------|------------|
| Consulter le catalogue | ✅ | ✅ | ✅ |
| Gérer son panier | ✅ | ✅ | ✅ |
| Passer une commande | ✅ | ✅ | ✅ |
| Consulter son historique | ✅ | ✅ | ✅ |
| Gérer les gallinacés | ❌ | ✅ | ✅ |
| Suivi journalier (ponte, etc.) | ❌ | ✅ | ✅ |
| Gérer les stocks | ❌ | ✅ | ✅ |
| Consulter les statistiques | ❌ | ✅ | ✅ |
| Gérer les commandes clients | ❌ | ✅ | ✅ |
| Gérer les utilisateurs | ❌ | ❌ | ✅ |
| Configurer l'application | ❌ | ❌ | ✅ |

---

## 3. Cas d'Utilisation (Use Cases)

### 3.1 Diagramme des Cas d'Utilisation (synthèse textuelle)
┌─────────────────────────────────────────────────────────────┐
│ APPLICATION FERME │
├─────────────────────────────────────────────────────────────┤
│ ┌───────────┐ ┌───────────┐ ┌───────────┐ │
│ │ Visiteur │ │ Client │ │ Fermier │ │
│ │ (ROLE_*) │ │(ROLE_USER)│ │(MANAGER) │ │
│ └─────┬─────┘ └─────┬─────┘ └─────┬─────┘ │
│ │ │ │ │
│ ▼ ▼ ▼ │
│ ┌─────────────────────────────────────────────────────┐ │
│ │ Consulter le catalogue │ │
│ ├─────────────────────────────────────────────────────┤ │
│ │ S'inscrire / Se connecter │ │
│ ├─────────────────────────────────────────────────────┤ │
│ │ Gérer son panier │ │
│ ├─────────────────────────────────────────────────────┤ │
│ │ Passer commande │ │
│ ├─────────────────────────────────────────────────────┤ │
│ │ Consulter historique │ │
│ ├─────────────────────────────────────────────────────┤ │
│ │ Suivi journalier │ │
│ ├─────────────────────────────────────────────────────┤ │
│ │ Gérer gallinacés │ │
│ ├─────────────────────────────────────────────────────┤ │
│ │ Gérer couveuses │ │
│ ├─────────────────────────────────────────────────────┤ │
│ │ Gérer stocks │ │
│ ├─────────────────────────────────────────────────────┤ │
│ │ Consulter statistiques │ │
│ ├─────────────────────────────────────────────────────┤ │
│ │ Gérer commandes clients │ │
│ └─────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘

---

### 3.2 Cas d'Utilisation Détaillés

#### UC1 - Consulter le catalogue

| Élément | Description |
|---------|-------------|
| **Acteur** | Visiteur, Client, Fermier, Admin |
| **Précondition** | Aucune |
| **Postcondition** | L'utilisateur visualise les produits disponibles (œufs, viande, tickets) |
| **Scénario nominal** | 1. L'utilisateur accède à la page d'accueil<br>2. Il consulte les catégories de produits<br>3. Il filtre par type (œufs, viande, tickets)<br>4. Il clique sur un produit pour voir les détails |
| **Scénario alternatif** | Aucun |
| **Règles métier** | - Les tickets ne sont disponibles que pour les dates à venir<br>- Les produits en stock sont affichés avec leur quantité disponible |

---

#### UC2 - S'inscrire / Se connecter

| Élément | Description |
|---------|-------------|
| **Acteur** | Visiteur |
| **Précondition** | L'utilisateur n'est pas connecté |
| **Postcondition** | L'utilisateur est authentifié et peut accéder aux fonctionnalités réservées |
| **Scénario nominal (inscription)** | 1. L'utilisateur clique sur "S'inscrire"<br>2. Il remplit le formulaire (email, mot de passe, nom, prénom, adresse)<br>3. Il valide son inscription<br>4. Un email de confirmation lui est envoyé |
| **Scénario nominal (connexion)** | 1. L'utilisateur clique sur "Se connecter"<br>2. Il saisit ses identifiants<br>3. Le système l'authentifie et le redirige vers son espace |
| **Scénario alternatif** | Mot de passe oublié → réinitialisation par email |
| **Règles métier** | - L'email doit être unique<br>- Le mot de passe doit faire au moins 8 caractères<br>- L'inscription est obligatoire pour passer commande |

---

#### UC3 - Gérer son panier

| Élément | Description |
|---------|-------------|
| **Acteur** | Client |
| **Précondition** | L'utilisateur est connecté (ROLE_USER) |
| **Postcondition** | Le panier est mis à jour avec les produits sélectionnés |
| **Scénario nominal** | 1. Le client ajoute un produit au panier<br>2. Le système vérifie la disponibilité en stock<br>3. Le client peut modifier les quantités<br>4. Le client peut supprimer des articles<br>5. Le total est automatiquement recalculé |
| **Scénario alternatif** | Stock insuffisant → message d'erreur |
| **Règles métier** | - Un produit ne peut pas être ajouté en quantité supérieure au stock disponible<br>- Les tickets sont ajoutés avec la date de visite choisie |

---

#### UC4 - Passer commande

| Élément | Description |
|---------|-------------|
| **Acteur** | Client |
| **Précondition** | - Client connecté<br>- Panier non vide |
| **Postcondition** | - Une commande est créée<br>- Les stocks sont diminués<br>- Un email de confirmation est envoyé |
| **Scénario nominal** | 1. Le client valide son panier<br>2. Il choisit son adresse de livraison<br>3. Il sélectionne une date de livraison<br>4. Il confirme la commande<br>5. Le système crée la commande et la livraison associée<br>6. Un email de confirmation est envoyé |
| **Scénario alternatif** | Paiement non simulé (le projet ne gère pas les paiements réels) |
| **Règles métier** | - Une commande est composée d'un ou plusieurs produits<br>- La livraison est créée automatiquement avec le statut "En préparation" |

---

#### UC5 - Consulter son historique

| Élément | Description |
|---------|-------------|
| **Acteur** | Client |
| **Précondition** | Client connecté |
| **Postcondition** | Le client visualise ses commandes passées |
| **Scénario nominal** | 1. Le client accède à son espace "Mes commandes"<br>2. Il visualise la liste de ses commandes (date, montant, statut)<br>3. Il peut cliquer sur une commande pour voir les détails |
| **Scénario alternatif** | Aucune commande → message "Vous n'avez pas encore passé de commande" |

---

#### UC6 - Suivi journalier de la ferme

| Élément | Description |
|---------|-------------|
| **Acteur** | Fermier (ROLE_MANAGER) |
| **Précondition** | L'utilisateur est connecté en tant que MANAGER |
| **Postcondition** | Une entrée de journal est enregistrée (ponte, incubation, naissance, abattage) |
| **Scénario nominal (ponte)** | 1. Le fermier accède à "Suivi du jour"<br>2. Il sélectionne "Ponte"<br>3. Il choisit la poule ou l'enclos<br>4. Il saisit le nombre d'œufs (et éventuellement fécondés)<br>5. Il valide → les stocks d'œufs sont augmentés |
| **Scénario nominal (incubation)** | 1. Le fermier sélectionne "Incubation"<br>2. Il choisit la couveuse<br>3. Il ajoute des œufs fécondés dans la couveuse<br>4. Il valide |
| **Scénario nominal (naissance)** | 1. Le fermier sélectionne "Naissance"<br>2. Il choisit la couveuse<br>3. Il saisit le nombre de poussins nés<br>4. Il valide → les poussins sont ajoutés dans l'enclos |
| **Scénario nominal (abattage)** | 1. Le fermier sélectionne "Abattage"<br>2. Il choisit l'enclos<br>3. Il saisit le nombre de poulets abattus<br>4. Il valide → la viande est ajoutée au stock |
| **Scénario alternatif** | Nombre d'œufs > stock disponible → erreur |
| **Règles métier** | - Un journal est horodaté automatiquement<br>- Une action "Ponte" peut générer des œufs de consommation OU des œufs fécondés pour couveuse<br>- Une couveuse a une capacité maximale |

---

#### UC7 - Gérer les gallinacés

| Élément | Description |
|---------|-------------|
| **Acteur** | Fermier (ROLE_MANAGER) |
| **Précondition** | L'utilisateur est connecté en tant que MANAGER |
| **Postcondition** | Un gallinacé est créé/modifié/supprimé |
| **Scénario nominal** | 1. Le fermier accède à "Gestion des gallinacés"<br>2. Il peut ajouter une nouvelle poule/coq/poussin<br>3. Il modifie les informations (âge, enclos, santé)<br>4. Il peut supprimer un gallinacé |
| **Règles métier** | - Un gallinacé est toujours dans un enclos<br>- Un poussin devient poule ou coq après 16 semaines<br>- Seules les poules pondent |

---

#### UC8 - Gérer les stocks

| Élément | Description |
|---------|-------------|
| **Acteur** | Fermier (ROLE_MANAGER) |
| **Précondition** | L'utilisateur est connecté en tant que MANAGER |
| **Postcondition** | Les stocks sont mis à jour |
| **Scénario nominal** | 1. Le fermier accède à "Gestion des stocks"<br>2. Il visualise les quantités disponibles (œufs, viande, tickets)<br>3. Il peut ajouter manuellement des quantités (réapprovisionnement)<br>4. Il peut définir des seuils d'alerte |
| **Scénario alternatif** | Stock < seuil → alerte affichée + email envoyé |
| **Règles métier** | - Les stocks évoluent automatiquement avec les actions du suivi journalier et les commandes<br>- Un ticket est un stock de places disponibles par date |

---

#### UC9 - Consulter les statistiques

| Élément | Description |
|---------|-------------|
| **Acteur** | Fermier (ROLE_MANAGER) |
| **Précondition** | L'utilisateur est connecté en tant que MANAGER |
| **Postcondition** | Le fermier visualise les indicateurs de performance |
| **Scénario nominal** | 1. Le fermier accède au "Dashboard"<br>2. Il visualise les statistiques :<br>   - Production moyenne d'œufs / mois<br>   - Taux de fécondation<br>   - Taux d'éclosion<br>   - Quantité de viande produite<br>   - Chiffre d'affaires estimé<br>3. Il peut filtrer par date |
| **Règles métier** | - Les statistiques sont calculées à partir des données du journal<br>- Le CA estimé est basé sur les prix de vente actuels |

---

#### UC10 - Gérer les commandes clients

| Élément | Description |
|---------|-------------|
| **Acteur** | Fermier (ROLE_MANAGER) |
| **Précondition** | L'utilisateur est connecté en tant que MANAGER |
| **Postcondition** | Une commande change de statut |
| **Scénario nominal** | 1. Le fermier accède à "Commandes clients"<br>2. Il visualise toutes les commandes (statut: En préparation, Expédiée, Livrée)<br>3. Il peut changer le statut d'une commande<br>4. Un email est automatiquement envoyé au client à chaque changement |
| **Règles métier** | - Le statut "Livrée" est terminal<br>- Une commande ne peut pas être modifiée après expédition |

---

## 4. Règles Métier Principales

### 4.1 Règles de Gestion des Gallinacés

| Règle | Description |
|-------|-------------|
| **RM-01** | Un gallinacé (poule, coq, poussin) appartient obligatoirement à un enclos |
| **RM-02** | Seules les poules (et non les coqs ou poussins) pondent des œufs |
| **RM-03** | Un poussin devient une poule ou un coq après 16 semaines |
| **RM-04** | Un coq peut féconder plusieurs poules dans le même enclos |

### 4.2 Règles de Production

| Règle | Description |
|-------|-------------|
| **RM-05** | Une poule pond en moyenne 0,8 œuf par jour (modélisé dans les fixtures) |
| **RM-06** | Un œuf fécondé peut être placé en couveuse |
| **RM-07** | L'incubation dure 21 jours (modélisé, pas géré en temps réel) |
| **RM-08** | Une couveuse a une capacité maximale configurable |

### 4.3 Règles Commerciales

| Règle | Description |
|-------|-------------|
| **RM-09** | Le prix des produits (œufs, viande, tickets) est défini par l'administrateur |
| **RM-10** | Un ticket de visite est valable pour une date spécifique |
| **RM-11** | Une commande est toujours associée à un client et une adresse de livraison |
| **RM-12** | Les stocks disponibles sont automatiquement décrémentés lors d'une commande validée |

### 4.4 Règles de Sécurité

| Règle | Description |
|-------|-------------|
| **RM-13** | Un client ne peut modifier que son propre panier (Voter) |
| **RM-14** | Les actions de gestion (ponte, abattage, etc.) sont réservées au rôle MANAGER |
| **RM-15** | Les mots de passe sont hachés avec le composant Security de Symfony |

---

## 5. Spécifications Techniques (synthèse)

### 5.1 Entités Principales (rappels)

| Entité | Type | Relations |
|--------|------|-----------|
| **Gallinacé** | Abstract (héritage) | - |
| Poule | Héritage | Enclos (ManyToOne) |
| Coq | Héritage | Enclos (ManyToOne) |
| Poussin | Héritage | Enclos (ManyToOne) |
| **Produit** | Abstract (héritage) | - |
| Œuf | Héritage | - |
| Viande | Héritage | - |
| Ticket | Héritage | - |
| **Client** | Héritage User | Commandes (OneToMany), Panier (OneToOne) |
| **Admin** | Héritage User | - |
| **Enclos** | - | Gallinacés (OneToMany) |
| **Couveuse** | - | Œufs fécondés (OneToMany) |
| **Panier** | - | Client (OneToOne), Produits (ManyToMany avec quantités) |
| **Commande** | - | Client (ManyToOne), Livraison (OneToOne) |
| **Livraison** | - | Commande (OneToOne) |
| **Stock** | - | Produit (ManyToOne) |
| **JournalFerme** | - | - (journal des actions) |

### 5.2 Pages Twig Attendues (10 minimum)

| Page | Description |
|------|-------------|
| Accueil | Présentation de la ferme et produits mis en avant |
| Catalogue | Liste des produits avec filtres |
| Détail produit | Fiche descriptive du produit |
| Panier | Récapitulatif des articles |
| Commande | Formulaire de validation |
| Mes commandes | Historique du client |
| Dashboard fermier | Suivi du jour et statistiques |
| Gestion gallinacés | Liste et CRUD |
| Gestion stocks | Visualisation et réapprovisionnement |
| Administration | Gestion des utilisateurs et paramètres |

---

## 6. Contraintes Non-Fonctionnelles

| Contrainte | Description |
|------------|-------------|
| **Performance** | Temps de réponse < 2s pour les pages principales |
| **Sécurité** | Toutes les routes sensibles protégées par Voter et rôles |
| **Qualité** | Tests unitaires et fonctionnels obligatoires |
| **CI/CD** | Pipeline GitHub Actions avec linter, PHPStan, tests |
| **Déploiement** | Application accessible en ligne (Render/Platform.sh) |
| **Documentation** | README complet avec guide d'installation et fixtures |

---

## 7. Glossaire

| Terme | Définition |
|-------|------------|
| **Gallinacé** | Famille d'oiseaux comprenant les poules, coqs et poussins |
| **Ponte** | Action de pondre des œufs |
| **Incubation** | Maintien des œufs à température constante pour l'éclosion |
| **Couveuse** | Appareil qui maintient les œufs à la bonne température |
| **Fécondé** | Œuf qui a été fertilisé par un coq et peut donner un poussin |
| **Ticket** | Entrée pour une visite de la ferme à une date précise |
| **Fixtures** | Jeu de données de test pour l'application |
| **Voter** | Composant Symfony pour gérer des permissions fines |

---

**Fin du Cahier des Charges**