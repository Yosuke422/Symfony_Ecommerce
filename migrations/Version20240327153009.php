<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240327153009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contenu_panier_produit (contenu_panier_id INT NOT NULL, produit_id INT NOT NULL, PRIMARY KEY(contenu_panier_id, produit_id))');
        $this->addSql('CREATE INDEX IDX_179C43E361405BF ON contenu_panier_produit (contenu_panier_id)');
        $this->addSql('CREATE INDEX IDX_179C43E3F347EFB ON contenu_panier_produit (produit_id)');
        $this->addSql('ALTER TABLE contenu_panier_produit ADD CONSTRAINT FK_179C43E361405BF FOREIGN KEY (contenu_panier_id) REFERENCES contenu_panier (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE contenu_panier_produit ADD CONSTRAINT FK_179C43E3F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE contenu_panier_produit DROP CONSTRAINT FK_179C43E361405BF');
        $this->addSql('ALTER TABLE contenu_panier_produit DROP CONSTRAINT FK_179C43E3F347EFB');
        $this->addSql('DROP TABLE contenu_panier_produit');
    }
}
