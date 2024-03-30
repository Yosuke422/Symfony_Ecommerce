<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240330123727 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_80507dc0f77d927c');
        $this->addSql('ALTER TABLE contenu_panier RENAME COLUMN quantiter TO quantite');
        $this->addSql('CREATE INDEX IDX_80507DC0F77D927C ON contenu_panier (panier_id)');
        $this->addSql('ALTER TABLE panier ALTER date_dachat DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX IDX_80507DC0F77D927C');
        $this->addSql('ALTER TABLE contenu_panier RENAME COLUMN quantite TO quantiter');
        $this->addSql('CREATE UNIQUE INDEX uniq_80507dc0f77d927c ON contenu_panier (panier_id)');
        $this->addSql('ALTER TABLE panier ALTER date_dachat SET NOT NULL');
    }
}
