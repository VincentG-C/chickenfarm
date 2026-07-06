<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260705174402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE commande_details_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE commandes_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE couveuses_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE enclos_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE gallinaces_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE gallinaces_nourritures_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE incubations_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE journal_ferme_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE livraisons_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE naissances_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE panier_produits_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE paniers_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE produits_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE stocks_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE commande_details (quantite INT NOT NULL, prix_unitaire NUMERIC(10, 2) NOT NULL, id INT NOT NULL, commande_id INT NOT NULL, produit_id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_849D792A82EA2E54 ON commande_details (commande_id)');
        $this->addSql('CREATE INDEX IDX_849D792AF347EFB ON commande_details (produit_id)');
        $this->addSql('CREATE TABLE commandes (date_commande TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, montant_total NUMERIC(10, 2) NOT NULL, statut VARCHAR(50) NOT NULL, reference VARCHAR(50) NOT NULL, notes TEXT DEFAULT NULL, id INT NOT NULL, client_id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_35D4282CAEA34913 ON commandes (reference)');
        $this->addSql('CREATE INDEX IDX_35D4282C19EB6921 ON commandes (client_id)');
        $this->addSql('CREATE TABLE couveuses (nom VARCHAR(100) NOT NULL, capacite INT NOT NULL, temperature NUMERIC(5, 2) DEFAULT NULL, humidite NUMERIC(5, 2) DEFAULT NULL, statut VARCHAR(50) NOT NULL, id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE enclos (nom VARCHAR(100) NOT NULL, superficie NUMERIC(10, 2) NOT NULL, capacite_max INT NOT NULL, type VARCHAR(50) NOT NULL, nb_gallinaces INT NOT NULL, id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE gallinaces (nom VARCHAR(100) NOT NULL, age INT NOT NULL, poids NUMERIC(6, 2) NOT NULL, sante VARCHAR(50) NOT NULL, date_entree TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, id INT NOT NULL, enclos_id INT NOT NULL, type VARCHAR(255) NOT NULL, cycle_ponte INT DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_418FBC38B1C0859 ON gallinaces (enclos_id)');
        $this->addSql('CREATE TABLE gallinaces_nourritures (quantite_par_jour NUMERIC(8, 2) NOT NULL, date_debut DATE DEFAULT NULL, date_fin DATE DEFAULT NULL, id INT NOT NULL, gallinace_id INT NOT NULL, nourriture_id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_582A92BB5B26DA07 ON gallinaces_nourritures (gallinace_id)');
        $this->addSql('CREATE INDEX IDX_582A92BB98BD5834 ON gallinaces_nourritures (nourriture_id)');
        $this->addSql('CREATE TABLE incubations (date_debut TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_fin_prevue TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_fin_reelle TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, nb_oeufs_debut INT NOT NULL, nb_oeufs_eclos INT NOT NULL, statut VARCHAR(50) NOT NULL, id INT NOT NULL, couveuse_id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_EA6A9A00FBBD7E99 ON incubations (couveuse_id)');
        $this->addSql('CREATE TABLE journal_ferme (date DATE NOT NULL, type VARCHAR(50) NOT NULL, description TEXT DEFAULT NULL, quantite INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, id INT NOT NULL, manager_id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_858D7DE8783E3463 ON journal_ferme (manager_id)');
        $this->addSql('CREATE TABLE livraisons (adresse_livraison TEXT NOT NULL, date_livraison_prevue TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_livraison_reelle TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, statut VARCHAR(50) NOT NULL, id INT NOT NULL, commande_id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_96A0CE6182EA2E54 ON livraisons (commande_id)');
        $this->addSql('CREATE TABLE naissances (date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, nb_poussins INT NOT NULL, nb_morts INT NOT NULL, id INT NOT NULL, incubation_id INT NOT NULL, enclos_id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_1C92D3CB9EB448E4 ON naissances (incubation_id)');
        $this->addSql('CREATE INDEX IDX_1C92D3CBB1C0859 ON naissances (enclos_id)');
        $this->addSql('CREATE TABLE panier_produits (quantite INT NOT NULL, date_ajout TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, id INT NOT NULL, panier_id INT NOT NULL, produit_id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_2468D6FEF77D927C ON panier_produits (panier_id)');
        $this->addSql('CREATE INDEX IDX_2468D6FEF347EFB ON panier_produits (produit_id)');
        $this->addSql('CREATE TABLE paniers (created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, id INT NOT NULL, client_id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4899903619EB6921 ON paniers (client_id)');
        $this->addSql('CREATE TABLE produits (nom VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, prix NUMERIC(10, 2) NOT NULL, image TEXT DEFAULT NULL, prix_unite VARCHAR(50) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, id INT NOT NULL, type VARCHAR(255) NOT NULL, calibre VARCHAR(50) DEFAULT NULL, est_feconde BOOLEAN DEFAULT NULL, date_ponte DATE DEFAULT NULL, poule_id INT DEFAULT NULL, poids_moyen NUMERIC(6, 2) DEFAULT NULL, type_decoupe VARCHAR(50) DEFAULT NULL, date_visite DATE DEFAULT NULL, nb_places INT DEFAULT NULL, nb_places_restantes INT DEFAULT NULL, duree_visite INT DEFAULT NULL, type_aliment VARCHAR(50) DEFAULT NULL, composition TEXT DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_BE2DDF8C26596FD8 ON produits (poule_id)');
        $this->addSql('CREATE TABLE stocks (quantite_disponible INT NOT NULL, seuil_alerte INT NOT NULL, date_derniere_entree TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_derniere_sortie TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, id INT NOT NULL, produit_id INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_56F79805F347EFB ON stocks (produit_id)');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, email VARCHAR(255) NOT NULL, password_hash VARCHAR(255) NOT NULL, prenom VARCHAR(100) NOT NULL, nom VARCHAR(100) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, roles JSON NOT NULL, type VARCHAR(255) NOT NULL, telephone VARCHAR(50) DEFAULT NULL, adresse_livraison TEXT DEFAULT NULL, adresse_facturation TEXT DEFAULT NULL, newsletter BOOLEAN DEFAULT NULL, code_employe VARCHAR(50) DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9C0A39725 ON users (code_employe)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT GENERATED BY DEFAULT AS IDENTITY NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
        $this->addSql('ALTER TABLE commande_details ADD CONSTRAINT FK_849D792A82EA2E54 FOREIGN KEY (commande_id) REFERENCES commandes (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE commande_details ADD CONSTRAINT FK_849D792AF347EFB FOREIGN KEY (produit_id) REFERENCES produits (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE commandes ADD CONSTRAINT FK_35D4282C19EB6921 FOREIGN KEY (client_id) REFERENCES users (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE gallinaces ADD CONSTRAINT FK_418FBC38B1C0859 FOREIGN KEY (enclos_id) REFERENCES enclos (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE gallinaces_nourritures ADD CONSTRAINT FK_582A92BB5B26DA07 FOREIGN KEY (gallinace_id) REFERENCES gallinaces (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE gallinaces_nourritures ADD CONSTRAINT FK_582A92BB98BD5834 FOREIGN KEY (nourriture_id) REFERENCES produits (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE incubations ADD CONSTRAINT FK_EA6A9A00FBBD7E99 FOREIGN KEY (couveuse_id) REFERENCES couveuses (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE journal_ferme ADD CONSTRAINT FK_858D7DE8783E3463 FOREIGN KEY (manager_id) REFERENCES users (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE livraisons ADD CONSTRAINT FK_96A0CE6182EA2E54 FOREIGN KEY (commande_id) REFERENCES commandes (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE naissances ADD CONSTRAINT FK_1C92D3CB9EB448E4 FOREIGN KEY (incubation_id) REFERENCES incubations (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE naissances ADD CONSTRAINT FK_1C92D3CBB1C0859 FOREIGN KEY (enclos_id) REFERENCES enclos (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE panier_produits ADD CONSTRAINT FK_2468D6FEF77D927C FOREIGN KEY (panier_id) REFERENCES paniers (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE panier_produits ADD CONSTRAINT FK_2468D6FEF347EFB FOREIGN KEY (produit_id) REFERENCES produits (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE paniers ADD CONSTRAINT FK_4899903619EB6921 FOREIGN KEY (client_id) REFERENCES users (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE produits ADD CONSTRAINT FK_BE2DDF8C26596FD8 FOREIGN KEY (poule_id) REFERENCES gallinaces (id)');
        $this->addSql('ALTER TABLE stocks ADD CONSTRAINT FK_56F79805F347EFB FOREIGN KEY (produit_id) REFERENCES produits (id) NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE commande_details_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE commandes_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE couveuses_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE enclos_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE gallinaces_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE gallinaces_nourritures_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE incubations_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE journal_ferme_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE livraisons_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE naissances_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE panier_produits_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE paniers_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE produits_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE stocks_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('ALTER TABLE commande_details DROP CONSTRAINT FK_849D792A82EA2E54');
        $this->addSql('ALTER TABLE commande_details DROP CONSTRAINT FK_849D792AF347EFB');
        $this->addSql('ALTER TABLE commandes DROP CONSTRAINT FK_35D4282C19EB6921');
        $this->addSql('ALTER TABLE gallinaces DROP CONSTRAINT FK_418FBC38B1C0859');
        $this->addSql('ALTER TABLE gallinaces_nourritures DROP CONSTRAINT FK_582A92BB5B26DA07');
        $this->addSql('ALTER TABLE gallinaces_nourritures DROP CONSTRAINT FK_582A92BB98BD5834');
        $this->addSql('ALTER TABLE incubations DROP CONSTRAINT FK_EA6A9A00FBBD7E99');
        $this->addSql('ALTER TABLE journal_ferme DROP CONSTRAINT FK_858D7DE8783E3463');
        $this->addSql('ALTER TABLE livraisons DROP CONSTRAINT FK_96A0CE6182EA2E54');
        $this->addSql('ALTER TABLE naissances DROP CONSTRAINT FK_1C92D3CB9EB448E4');
        $this->addSql('ALTER TABLE naissances DROP CONSTRAINT FK_1C92D3CBB1C0859');
        $this->addSql('ALTER TABLE panier_produits DROP CONSTRAINT FK_2468D6FEF77D927C');
        $this->addSql('ALTER TABLE panier_produits DROP CONSTRAINT FK_2468D6FEF347EFB');
        $this->addSql('ALTER TABLE paniers DROP CONSTRAINT FK_4899903619EB6921');
        $this->addSql('ALTER TABLE produits DROP CONSTRAINT FK_BE2DDF8C26596FD8');
        $this->addSql('ALTER TABLE stocks DROP CONSTRAINT FK_56F79805F347EFB');
        $this->addSql('DROP TABLE commande_details');
        $this->addSql('DROP TABLE commandes');
        $this->addSql('DROP TABLE couveuses');
        $this->addSql('DROP TABLE enclos');
        $this->addSql('DROP TABLE gallinaces');
        $this->addSql('DROP TABLE gallinaces_nourritures');
        $this->addSql('DROP TABLE incubations');
        $this->addSql('DROP TABLE journal_ferme');
        $this->addSql('DROP TABLE livraisons');
        $this->addSql('DROP TABLE naissances');
        $this->addSql('DROP TABLE panier_produits');
        $this->addSql('DROP TABLE paniers');
        $this->addSql('DROP TABLE produits');
        $this->addSql('DROP TABLE stocks');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
