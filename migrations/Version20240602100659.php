<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240602100659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE housing ADD latitude VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE housing ADD longitude VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE housing ADD postal_code VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE housing ADD city VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE housing ADD street VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE housing DROP latitude');
        $this->addSql('ALTER TABLE housing DROP longitude');
        $this->addSql('ALTER TABLE housing DROP postal_code');
        $this->addSql('ALTER TABLE housing DROP city');
        $this->addSql('ALTER TABLE housing DROP street');
    }
}