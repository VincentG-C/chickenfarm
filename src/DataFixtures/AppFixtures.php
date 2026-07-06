<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Client;
use App\Entity\Commande;
use App\Entity\CommandeDetail;
use App\Entity\Coq;
use App\Entity\Couveuse;
use App\Entity\Enclos;
use App\Entity\GallinaceNourriture;
use App\Entity\Incubation;
use App\Entity\JournalFerme;
use App\Entity\Livraison;
use App\Entity\Manager;
use App\Entity\Naissance;
use App\Entity\Nourriture;
use App\Entity\Oeuf;
use App\Entity\Panier;
use App\Entity\PanierProduit;
use App\Entity\Poule;
use App\Entity\Poussin;
use App\Entity\Produit;
use App\Entity\Stock;
use App\Entity\Ticket;
use App\Entity\Viande;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create('fr_FR');

        // ──────────────────────────────────────────────
        // 1. UTILISATEURS
        // ──────────────────────────────────────────────

        // Admin
        $admin = new Admin();
        $admin->setPrenom('Super');
        $admin->setNom('Admin');
        $admin->setEmail('admin@ferme.fr');
        $admin->setPasswordHash($this->passwordHasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        // Managers
        $journalManagers = [];
        $managerData = [
            ['prenom' => 'Jean', 'nom' => 'Dupont', 'email' => 'manager@ferme.fr', 'tel' => '0612345678', 'code' => 'EMP001'],
            ['prenom' => 'Sophie', 'nom' => 'Martin', 'email' => 'sophie@ferme.fr', 'tel' => '0698765432', 'code' => 'EMP002'],
        ];
        foreach ($managerData as $data) {
            $m = new Manager();
            $m->setPrenom($data['prenom']);
            $m->setNom($data['nom']);
            $m->setEmail($data['email']);
            $m->setTelephone($data['tel']);
            $m->setCodeEmploye($data['code']);
            $m->setPasswordHash($this->passwordHasher->hashPassword($m, 'manager123'));
            $manager->persist($m);
            $journalManagers[] = $m;
        }

        // Clients (5)
        $clients = [];
        $clientData = [
            ['prenom' => 'Paul', 'nom' => 'Durand', 'email' => 'paul@test.fr', 'tel' => '0712345678', 'adrLiv' => '12 Rue de la Paix, 75001 Paris', 'adrFac' => '12 Rue de la Paix, 75001 Paris'],
            ['prenom' => 'Marie', 'nom' => 'Leroy', 'email' => 'marie@test.fr', 'tel' => '0798765432', 'adrLiv' => '8 Avenue des Fleurs, 69001 Lyon', 'adrFac' => '8 Avenue des Fleurs, 69001 Lyon'],
            ['prenom' => 'Pierre', 'nom' => 'Moreau', 'email' => 'pierre@test.fr', 'tel' => '0787654321', 'adrLiv' => '15 Boulevard Saint-Michel, 75005 Paris', 'adrFac' => '15 Boulevard Saint-Michel, 75005 Paris'],
            ['prenom' => 'Camille', 'nom' => 'Petit', 'email' => 'camille@test.fr', 'tel' => '0765432198', 'adrLiv' => '3 Place du Capitole, 31000 Toulouse', 'adrFac' => '3 Place du Capitole, 31000 Toulouse'],
            ['prenom' => 'Lucas', 'nom' => 'Bernard', 'email' => 'lucas@test.fr', 'tel' => '0745678912', 'adrLiv' => '42 Rue de la République, 13001 Marseille', 'adrFac' => '42 Rue de la République, 13001 Marseille'],
        ];
        foreach ($clientData as $i => $data) {
            $c = new Client();
            $c->setPrenom($data['prenom']);
            $c->setNom($data['nom']);
            $c->setEmail($data['email']);
            $c->setTelephone($data['tel']);
            $c->setAdresseLivraison($data['adrLiv']);
            $c->setAdresseFacturation($data['adrFac']);
            $c->setNewsletter($i % 2 === 0);
            $c->setPasswordHash($this->passwordHasher->hashPassword($c, 'client123'));
            $manager->persist($c);
            $clients[] = $c;
        }

        $manager->flush();

        // ──────────────────────────────────────────────
        // 2. ENCLOS (5)
        // ──────────────────────────────────────────────
        $enclosList = [];
        foreach ([
            ['nom' => 'Poulailler Principal', 'superficie' => 250, 'capacite' => 40, 'type' => 'poulailler'],
            ['nom' => 'Enclos Nord', 'superficie' => 500, 'capacite' => 60, 'type' => 'enclos_exterieur'],
            ['nom' => 'Volière aux Poussins', 'superficie' => 100, 'capacite' => 30, 'type' => 'voliere'],
            ['nom' => 'Poulailler Sud', 'superficie' => 200, 'capacite' => 35, 'type' => 'poulailler'],
            ['nom' => 'Grand Enclos Est', 'superficie' => 800, 'capacite' => 80, 'type' => 'enclos_exterieur'],
        ] as $data) {
            $e = new Enclos();
            $e->setNom($data['nom']);
            $e->setSuperficie((string) $data['superficie']);
            $e->setCapaciteMax($data['capacite']);
            $e->setType($data['type']);
            $manager->persist($e);
            $enclosList[] = $e;
        }
        $manager->flush();

        // ──────────────────────────────────────────────
        // 3. GALLINACES
        // ──────────────────────────────────────────────
        $poules = [];
        foreach (['Blanche', 'Rousse', 'Noire', 'Grise', 'Dorée', 'Pépite', 'Coquette', 'Marguerite'] as $i => $nom) {
            $p = new Poule();
            $p->setNom($nom);
            $p->setAge(mt_rand(1, 36));
            $p->setPoids((string) round(1.5 + mt_rand(0, 20) / 10, 2));
            $p->setSante(['excellent', 'bon', 'bon', 'moyen'][array_rand(['excellent', 'bon', 'bon', 'moyen'])]);
            $p->setEnclos($enclosList[$i % 5]);
            $p->setCyclePonte(mt_rand(3, 7));
            $enclosList[$i % 5]->setNbGallinaces($enclosList[$i % 5]->getNbGallinaces() + 1);
            $manager->persist($p);
            $poules[] = $p;
        }

        foreach (['Gertrude', 'Cocorico', 'Fier-à-bras'] as $i => $nom) {
            $c = new Coq();
            $c->setNom($nom);
            $c->setAge(mt_rand(12, 48));
            $c->setPoids((string) round(2.5 + mt_rand(0, 15) / 10, 2));
            $c->setSante('bon');
            $c->setEnclos($enclosList[$i % 5]);
            $enclosList[$i % 5]->setNbGallinaces($enclosList[$i % 5]->getNbGallinaces() + 1);
            $manager->persist($c);
        }

        foreach (['Pipou', 'Chipie', 'Minus', 'Globi', 'Plumette'] as $i => $nom) {
            $p = new Poussin();
            $p->setNom($nom);
            $p->setAge(mt_rand(0, 2));
            $p->setPoids((string) round(0.3 + mt_rand(0, 15) / 100, 2));
            $p->setSante('excellent');
            $p->setEnclos($enclosList[2]); // Volière
            $enclosList[2]->setNbGallinaces($enclosList[2]->getNbGallinaces() + 1);
            $manager->persist($p);
        }
        $manager->flush();

        // ──────────────────────────────────────────────
        // 4. PRODUITS
        // ──────────────────────────────────────────────
        $produits = [];

        // Œufs (4)
        $oeufData = [
            ['nom' => 'Œufs frais - Calibre moyen', 'prix' => '3.50', 'unite' => 'la demi-douzaine', 'calibre' => 'moyen', 'feconde' => false],
            ['nom' => 'Gros œufs fermiers', 'prix' => '4.50', 'unite' => 'la demi-douzaine', 'calibre' => 'gros', 'feconde' => false],
            ['nom' => 'Œufs extra-gros', 'prix' => '5.50', 'unite' => 'la demi-douzaine', 'calibre' => 'extra_gros', 'feconde' => false],
            ['nom' => 'Œufs fécondés pour couvaison', 'prix' => '7.00', 'unite' => 'la demi-douzaine', 'calibre' => 'gros', 'feconde' => true],
        ];
        foreach ($oeufData as $i => $data) {
            $o = new Oeuf();
            $o->setNom($data['nom']);
            $o->setDescription('Œufs frais de la ferme, qualité supérieure.');
            $o->setPrix($data['prix']);
            $o->setPrixUnite($data['unite']);
            $o->setCalibre($data['calibre']);
            $o->setEstFeconde($data['feconde']);
            $o->setDatePonte((new \DateTimeImmutable())->modify('-'.($i + 1).' days'));
            $o->setPoule($poules[$i]);
            $manager->persist($o);
            $produits[] = $o;
        }

        // Viandes (2)
        $v = new Viande();
        $v->setNom('Poulet fermier entier');
        $v->setDescription('Poulet fermier élevé en plein air. Viande tendre et savoureuse.');
        $v->setPrix('12.50');
        $v->setPrixUnite('le kg');
        $v->setPoidsMoyen('2.5');
        $v->setTypeDecoupe('entier');
        $manager->persist($v);
        $produits[] = $v;

        $v2 = new Viande();
        $v2->setNom('Cuisses de poulet (x4)');
        $v2->setDescription('Lot de 4 cuisses de poulet fermier. Idéal pour vos grillades.');
        $v2->setPrix('8.90');
        $v2->setPrixUnite('le lot');
        $v2->setPoidsMoyen('1.2');
        $v2->setTypeDecoupe('cuisse');
        $manager->persist($v2);
        $produits[] = $v2;

        // Tickets (3)
        $ticketData = [
            ['nom' => 'Visite guidée de la ferme', 'prix' => '12.00', 'unite' => 'par personne', 'duree' => 60, 'places' => 20],
            ['nom' => 'Atelier œufs & pâtisserie', 'prix' => '25.00', 'unite' => 'par personne', 'duree' => 120, 'places' => 15],
            ['nom' => 'Journée à la ferme (formule famille)', 'prix' => '45.00', 'unite' => 'par personne', 'duree' => 360, 'places' => 30],
        ];
        foreach ($ticketData as $i => $data) {
            $t = new Ticket();
            $t->setNom($data['nom']);
            $t->setDescription('Découvrez notre ferme !');
            $t->setPrix($data['prix']);
            $t->setPrixUnite($data['unite']);
            $t->setDateVisite((new \DateTimeImmutable())->modify('+'.($i + 2).' days'));
            $t->setNbPlaces($data['places']);
            $t->setNbPlacesRestantes($data['places']);
            $t->setDureeVisite($data['duree']);
            $manager->persist($t);
            $produits[] = $t;
        }

        // Nourritures (3)
        $nourData = [
            ['nom' => 'Mélange de grains bio (5kg)', 'prix' => '8.50', 'unite' => 'le sac', 'type' => 'graines', 'compo' => 'Maïs*, Blé*, Orge*, Coquilles d\'huîtres broyées'],
            ['nom' => 'Granulés pour poussins (2kg)', 'prix' => '6.90', 'unite' => 'le sac', 'type' => 'granules', 'compo' => 'Maïs, Tourteau de soja, Blé, Minéraux'],
            ['nom' => 'Complément calcium coquilles', 'prix' => '4.50', 'unite' => 'le sachet', 'type' => 'calcium', 'compo' => 'Carbonate de calcium 99%, Vitamine D3'],
        ];
        foreach ($nourData as $data) {
            $n = new Nourriture();
            $n->setNom($data['nom']);
            $n->setDescription('Alimentation de qualité pour vos volailles.');
            $n->setPrix($data['prix']);
            $n->setPrixUnite($data['unite']);
            $n->setTypeAliment($data['type']);
            $n->setComposition($data['compo']);
            $manager->persist($n);
            $produits[] = $n;
        }

        $manager->flush();

        // ──────────────────────────────────────────────
        // 5. STOCKS
        // ──────────────────────────────────────────────
        $stockQt = [48, 36, 24, 12, 8, 15, 50, 30, 20, 25, 18, 3];
        $stockSeuil = [12, 12, 6, 6, 3, 5, 10, 5, 5, 5, 5, 5];
        foreach ($produits as $i => $produit) {
            $s = new Stock();
            $s->setProduit($produit);
            $s->setQuantiteDisponible($stockQt[$i]);
            $s->setSeuilAlerte($stockSeuil[$i]);
            $s->setDateDerniereEntree((new \DateTimeImmutable())->modify('-'.mt_rand(0, 5).' days'));
            $manager->persist($s);
        }
        $manager->flush();

        // ──────────────────────────────────────────────
        // 6. PANIERS
        // ──────────────────────────────────────────────
        foreach ($clients as $i => $client) {
            $panier = new Panier();
            $panier->setClient($client);
            $manager->persist($panier);

            if ($i < 3) {
                $pp = new PanierProduit();
                $pp->setPanier($panier);
                $pp->setProduit($produits[$i]);
                $pp->setQuantite(mt_rand(1, 3));
                $manager->persist($pp);
            }
        }
        $manager->flush();

        // ──────────────────────────────────────────────
        // 7. COMMANDES
        // ──────────────────────────────────────────────
        $statuses = ['en_attente', 'confirmee', 'en_preparation', 'expediee', 'livree', 'annulee'];
        $references = [];

        foreach ($clients as $client) {
            foreach (range(1, mt_rand(2, 4)) as $j) {
                $commande = new Commande();
                $commande->setClient($client);
                $commande->setDateCommande((new \DateTimeImmutable())->modify('-'.mt_rand(1, 90).' days'));
                $commande->setStatut($statuses[array_rand($statuses)]);

                $ref = 'CMD-'.strtoupper(substr($client->getNom() ?? 'XXX', 0, 3)).'-'.str_pad((string) $j, 3, '0', STR_PAD_LEFT);
                while (in_array($ref, $references, true)) {
                    $ref = 'CMD-'.strtoupper(substr($client->getNom() ?? 'XXX', 0, 3)).'-'.str_pad((string) mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
                }
                $references[] = $ref;
                $commande->setReference($ref);
                $commande->setNotes($faker->optional(0.3)->sentence());

                $total = 0;
                $numDetails = min(mt_rand(1, 3), count($produits));
                $keys = (array) array_rand($produits, $numDetails);
                foreach ($keys as $k) {
                    $detail = new CommandeDetail();
                    $detail->setCommande($commande);
                    $detail->setProduit($produits[$k]);
                    $qte = mt_rand(1, 5);
                    $detail->setQuantite($qte);
                    $detail->setPrixUnitaire($produits[$k]->getPrix() ?? '0');
                    $total += (float) ($produits[$k]->getPrix() ?? 0) * $qte;
                    $manager->persist($detail);
                }
                $commande->setMontantTotal((string) round($total, 2));

                if (in_array($commande->getStatut(), ['confirmee', 'en_preparation', 'expediee', 'livree'], true)) {
                    $liv = new Livraison();
                    $liv->setCommande($commande);
                    $liv->setAdresseLivraison($client->getAdresseLivraison() ?? 'Adresse non spécifiée');
                    $liv->setDateLivraisonPrevue((new \DateTimeImmutable())->modify('+'.mt_rand(1, 7).' days'));
                    $liv->setStatut(
                        match ($commande->getStatut()) {
                            'livree' => 'livree',
                            'expediee' => 'expediee',
                            default => 'en_preparation'
                        }
                    );
                    $manager->persist($liv);
                }

                $manager->persist($commande);
            }
        }
        $manager->flush();

        // ──────────────────────────────────────────────
        // 8. COUVEUSES & INCUBATIONS
        // ──────────────────────────────────────────────
        $couveuse1 = new Couveuse();
        $couveuse1->setNom('Couveuse A - Automatique');
        $couveuse1->setCapacite(24);
        $couveuse1->setTemperature('37.5');
        $couveuse1->setHumidite('55');
        $couveuse1->setStatut('en_marche');
        $manager->persist($couveuse1);

        $couveuse2 = new Couveuse();
        $couveuse2->setNom('Couveuse B - Manuelle');
        $couveuse2->setCapacite(60);
        $couveuse2->setTemperature('37.8');
        $couveuse2->setHumidite('50');
        $couveuse2->setStatut('disponible');
        $manager->persist($couveuse2);

        $manager->flush();

        // Incubation en cours
        $inc1 = new Incubation();
        $inc1->setCouveuse($couveuse1);
        $inc1->setDateDebut((new \DateTimeImmutable())->modify('-14 days'));
        $inc1->setDateFinPrevue((new \DateTimeImmutable())->modify('+7 days'));
        $inc1->setNbOeufsDebut(18);
        $inc1->setNbOeufsEclos(0);
        $inc1->setStatut('en_cours');
        $manager->persist($inc1);

        // Incubation terminée
        $inc2 = new Incubation();
        $inc2->setCouveuse($couveuse1);
        $inc2->setDateDebut((new \DateTimeImmutable())->modify('-22 days'));
        $inc2->setDateFinPrevue((new \DateTimeImmutable())->modify('-1 days'));
        $inc2->setNbOeufsDebut(12);
        $inc2->setNbOeufsEclos(9);
        $inc2->setStatut('terminee');
        $manager->persist($inc2);

        $manager->flush();

        // Naissance
        $naiss = new Naissance();
        $naiss->setIncubation($inc2);
        $naiss->setEnclos($enclosList[2]); // Volière
        $naiss->setDate((new \DateTimeImmutable())->modify('-1 days'));
        $naiss->setNbPoussins(9);
        $naiss->setNbMorts(0);
        $manager->persist($naiss);

        $manager->flush();

        // ──────────────────────────────────────────────
        // 9. JOURNAL DE FERME
        // ──────────────────────────────────────────────
        $journalTypes = ['ponte', 'incubation', 'naissance', 'soin', 'alimentation', 'autre'];
        $entries = [
            'Nettoyage complet du poulailler principal – litière changée et désinfection effectuée.',
            'Vaccination annuelle des poules contre la maladie de Newcastle.',
            '85 œufs récoltés aujourd’hui – nouveau record de la saison !',
            '9 poussins nés dans la couveuse A, installés dans la volière.',
            'Commande de 200kg de mélange grain bio passée chez le fournisseur.',
            'Réparation d\'une brèche dans la clôture de l\'enclos nord après les intempéries.',
            'Visite annuelle de contrôle sanitaire – avis favorable.',
            'Nouveau lot de 40 œufs fécondés mis en incubation dans la couveuse A.',
        ];
        foreach ($entries as $i => $desc) {
            $j = new JournalFerme();
            $j->setManager($journalManagers[$i % 2]);
            $j->setDate((new \DateTimeImmutable())->modify('-'.mt_rand(0, 30).' days'));
            $j->setType($journalTypes[$i % count($journalTypes)]);
            $j->setDescription($desc);
            $j->setQuantite(mt_rand(0, 100));
            $manager->persist($j);
        }

        // ──────────────────────────────────────────────
        // 10. GALLINACES NOURRITURES
        // ──────────────────────────────────────────────
        $gn = new GallinaceNourriture();
        $gn->setGallinace($poules[0]);
        $gn->setNourriture($produits[array_key_last($produits) - 2]); // premier aliment
        $gn->setQuantiteParJour('0.500');
        $gn->setDateDebut((new \DateTimeImmutable())->modify('-30 days'));
        $manager->persist($gn);

        $manager->flush();
    }
}
