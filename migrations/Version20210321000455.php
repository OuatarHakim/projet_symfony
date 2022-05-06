<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210321000455 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Equipe CHANGE id_equipe id_equipe CHAR(3) NOT NULL');
        $this->addSql('ALTER TABLE Joueur CHANGE id_equipe id_equipe CHAR(3) NOT NULL, CHANGE id_poste_id id_poste INT NOT NULL');
        $this->addSql('ALTER TABLE Joueur ADD CONSTRAINT FK_FADDACF3920C4E9B FOREIGN KEY (id_poste) REFERENCES Poste (id_poste)');
        $this->addSql('CREATE INDEX IDX_FADDACF3920C4E9B ON Joueur (id_poste)');
        $this->addSql('ALTER TABLE Joueur RENAME INDEX fk_fd71a9c527e0ff8 TO IDX_FADDACF327E0FF8');
        $this->addSql('DROP INDEX nom ON Poste');
        $this->addSql('ALTER TABLE Poste CHANGE nom nom VARCHAR(200) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Equipe CHANGE id_equipe id_equipe CHAR(3) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`');
        $this->addSql('ALTER TABLE Joueur DROP FOREIGN KEY FK_FADDACF3920C4E9B');
        $this->addSql('DROP INDEX IDX_FADDACF3920C4E9B ON Joueur');
        $this->addSql('ALTER TABLE Joueur CHANGE id_equipe id_equipe CHAR(3) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, CHANGE id_poste id_poste_id INT NOT NULL');
        $this->addSql('ALTER TABLE Joueur RENAME INDEX idx_faddacf327e0ff8 TO FK_FD71A9C527E0FF8');
        $this->addSql('ALTER TABLE Poste CHANGE nom nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`');
        $this->addSql('CREATE UNIQUE INDEX nom ON Poste (nom)');
    }
}
