<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240602154546 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE chamber_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE chamber (id INT NOT NULL, housing_id INT NOT NULL, price NUMERIC(10, 2) NOT NULL, avaible_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, caution NUMERIC(10, 2) NOT NULL, surface_area NUMERIC(6, 2) NOT NULL, furnished BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4359E5AEAD5873E3 ON chamber (housing_id)');
        $this->addSql('ALTER TABLE chamber ADD CONSTRAINT FK_4359E5AEAD5873E3 FOREIGN KEY (housing_id) REFERENCES housing (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE chamber_id_seq CASCADE');
        $this->addSql('ALTER TABLE chamber DROP CONSTRAINT FK_4359E5AEAD5873E3');
        $this->addSql('DROP TABLE chamber');
    }
}
