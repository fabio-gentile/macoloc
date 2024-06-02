<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240602095439 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE housing_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE housing (id INT NOT NULL, user_id INT NOT NULL, price NUMERIC(10, 2) NOT NULL, type VARCHAR(255) NOT NULL, number_of_rooms INT NOT NULL, surface_area NUMERIC(6, 2) NOT NULL, avaible_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, description TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, commodity JSON DEFAULT NULL, other JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FB8142C3A76ED395 ON housing (user_id)');
        $this->addSql('COMMENT ON COLUMN housing.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE housing ADD CONSTRAINT FK_FB8142C3A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE housing_id_seq CASCADE');
        $this->addSql('ALTER TABLE housing DROP CONSTRAINT FK_FB8142C3A76ED395');
        $this->addSql('DROP TABLE housing');
    }
}
