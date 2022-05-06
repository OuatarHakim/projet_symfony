<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210320225205 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE joueur DROP FOREIGN KEY joueur_ibfk_1');
        $this->addSql('CREATE TABLE membre_personnel (id INT AUTO_INCREMENT NOT NULL, id_memper INT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, id_equipe VARCHAR(3) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE joueur');
        $this->addSql('DROP TABLE `match`');
        $this->addSql('DROP TABLE membrepersonnel');
        $this->addSql('DROP TABLE poste');
        $this->addSql('DROP TABLE pouleequipe');
        $this->addSql('ALTER TABLE equipe CHANGE id_equipe id_equipe CHAR(3) NOT NULL, CHANGE nbr_victoire nbr_victoire INT NOT NULL, CHANGE nbr_defaite nbr_defaite INT NOT NULL');
        $this->addSql('ALTER TABLE poule ADD id INT AUTO_INCREMENT NOT NULL, CHANGE id_poule id_poule VARCHAR(1) NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE joueur (id_joueur INT AUTO_INCREMENT NOT NULL, id_poste INT NOT NULL, id_equipe CHAR(3) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, prenom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, age INT NOT NULL, taille DOUBLE PRECISION NOT NULL, INDEX id_poste (id_poste), INDEX id_equipe (id_equipe), PRIMARY KEY(id_joueur)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE `match` (id_equipe1 CHAR(3) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, id_equipe2 CHAR(3) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, date DATE NOT NULL, score_equipe1 INT NOT NULL, score_equipe2 INT NOT NULL, INDEX id_equipe2 (id_equipe2), INDEX IDX_7A5BC50530B8EB96 (id_equipe1), PRIMARY KEY(id_equipe1, id_equipe2, date)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE membrepersonnel (id_memper INT AUTO_INCREMENT NOT NULL, id_equipe CHAR(3) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, prenom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, role VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX id_equipe (id_equipe), PRIMARY KEY(id_memper)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE poste (id_poste INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, UNIQUE INDEX nom (nom), PRIMARY KEY(id_poste)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE pouleequipe (id_equipe CHAR(3) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, id_poule CHAR(1) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, pts INT DEFAULT 0, nbr_vic_poule INT DEFAULT 0, nbr_def_poule INT DEFAULT 0, INDEX id_poule (id_poule), INDEX IDX_C43CF77827E0FF8 (id_equipe), PRIMARY KEY(id_equipe, id_poule)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE joueur ADD CONSTRAINT joueur_ibfk_1 FOREIGN KEY (id_poste) REFERENCES poste (id_poste)');
        $this->addSql('ALTER TABLE joueur ADD CONSTRAINT joueur_ibfk_2 FOREIGN KEY (id_equipe) REFERENCES equipe (id_equipe)');
        $this->addSql('ALTER TABLE `match` ADD CONSTRAINT match_ibfk_1 FOREIGN KEY (id_equipe1) REFERENCES equipe (id_equipe)');
        $this->addSql('ALTER TABLE `match` ADD CONSTRAINT match_ibfk_2 FOREIGN KEY (id_equipe2) REFERENCES equipe (id_equipe)');
        $this->addSql('ALTER TABLE membrepersonnel ADD CONSTRAINT membrepersonnel_ibfk_1 FOREIGN KEY (id_equipe) REFERENCES equipe (id_equipe)');
        $this->addSql('ALTER TABLE pouleequipe ADD CONSTRAINT pouleequipe_ibfk_1 FOREIGN KEY (id_equipe) REFERENCES equipe (id_equipe)');
        $this->addSql('ALTER TABLE pouleequipe ADD CONSTRAINT pouleequipe_ibfk_2 FOREIGN KEY (id_poule) REFERENCES poule (id_poule)');
        $this->addSql('DROP TABLE membre_personnel');
        $this->addSql('ALTER TABLE Equipe CHANGE id_equipe id_equipe CHAR(3) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE nbr_victoire nbr_victoire INT DEFAULT 0, CHANGE nbr_defaite nbr_defaite INT DEFAULT 0');
        $this->addSql('ALTER TABLE poule MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE poule DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE poule DROP id, CHANGE id_poule id_poule CHAR(1) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE poule ADD PRIMARY KEY (id_poule)');
    }
}
