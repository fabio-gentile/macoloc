<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240605092927 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE tenant_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE tenant (id INT NOT NULL, user_id INT NOT NULL, title VARCHAR(255) NOT NULL, gender VARCHAR(255) NOT NULL, activity VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, budget NUMERIC(7, 2) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, longitude VARCHAR(255) NOT NULL, latitude VARCHAR(255) NOT NULL, description TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4E59C462A76ED395 ON tenant (user_id)');
        $this->addSql('COMMENT ON COLUMN tenant.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE tenant ADD CONSTRAINT FK_4E59C462A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE tenant_id_seq CASCADE');
        $this->addSql('ALTER TABLE tenant DROP CONSTRAINT FK_4E59C462A76ED395');
        $this->addSql('DROP TABLE tenant');
    }
}
