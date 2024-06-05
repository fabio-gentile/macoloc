<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240605093226 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE tenant_image_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE tenant_image (id INT NOT NULL, tenant_id INT DEFAULT NULL, mime_type VARCHAR(255) NOT NULL, filename VARCHAR(255) NOT NULL, original_filename VARCHAR(255) NOT NULL, uploaded_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DAADE86B9033212A ON tenant_image (tenant_id)');
        $this->addSql('COMMENT ON COLUMN tenant_image.uploaded_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE tenant_image ADD CONSTRAINT FK_DAADE86B9033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE tenant_image_id_seq CASCADE');
        $this->addSql('ALTER TABLE tenant_image DROP CONSTRAINT FK_DAADE86B9033212A');
        $this->addSql('DROP TABLE tenant_image');
    }
}
