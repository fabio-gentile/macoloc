<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240617121823 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE french_city_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reset_password_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE chamber (id UUID NOT NULL, housing_id UUID NOT NULL, price NUMERIC(10, 2) NOT NULL, avaible_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, caution NUMERIC(10, 2) NOT NULL, surface_area NUMERIC(6, 2) NOT NULL, furnished BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4359E5AEAD5873E3 ON chamber (housing_id)');
        $this->addSql('COMMENT ON COLUMN chamber.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN chamber.housing_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN chamber.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE conversation (id UUID NOT NULL, user_one_id UUID NOT NULL, user_two_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8A8E26E99EC8D52E ON conversation (user_one_id)');
        $this->addSql('CREATE INDEX IDX_8A8E26E9F59432E1 ON conversation (user_two_id)');
        $this->addSql('COMMENT ON COLUMN conversation.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN conversation.user_one_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN conversation.user_two_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN conversation.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE french_city (id INT NOT NULL, department VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, postal_code VARCHAR(255) NOT NULL, longitude VARCHAR(255) NOT NULL, latitude VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE housing (id UUID NOT NULL, user_id UUID NOT NULL, price NUMERIC(10, 2) DEFAULT NULL, type VARCHAR(255) NOT NULL, number_of_rooms INT NOT NULL, surface_area NUMERIC(6, 2) NOT NULL, avaible_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, description TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, commodity JSONB DEFAULT NULL, other JSON DEFAULT NULL, latitude VARCHAR(255) NOT NULL, longitude VARCHAR(255) NOT NULL, postal_code VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FB8142C3A76ED395 ON housing (user_id)');
        $this->addSql('COMMENT ON COLUMN housing.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN housing.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN housing.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE housing_image (id UUID NOT NULL, housing_id UUID NOT NULL, mime_type VARCHAR(255) NOT NULL, filename VARCHAR(255) NOT NULL, original_filename VARCHAR(255) NOT NULL, uploaded_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_163EC29FAD5873E3 ON housing_image (housing_id)');
        $this->addSql('COMMENT ON COLUMN housing_image.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN housing_image.housing_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN housing_image.uploaded_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE message (id UUID NOT NULL, sender_id UUID NOT NULL, conversation_id UUID DEFAULT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B6BD307FF624B39D ON message (sender_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F9AC0396 ON message (conversation_id)');
        $this->addSql('COMMENT ON COLUMN message.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN message.sender_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN message.conversation_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN message.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE newsletter_reference (id UUID NOT NULL, subscriber_id UUID NOT NULL, unsubscribe_token UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, sent_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_319C393E0674361 ON newsletter_reference (unsubscribe_token)');
        $this->addSql('CREATE INDEX IDX_319C3937808B1AD ON newsletter_reference (subscriber_id)');
        $this->addSql('COMMENT ON COLUMN newsletter_reference.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN newsletter_reference.subscriber_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN newsletter_reference.unsubscribe_token IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN newsletter_reference.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE newsletter_subscriber (id UUID NOT NULL, email VARCHAR(255) NOT NULL, subscribed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_verified BOOLEAN NOT NULL, last_sent_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN newsletter_subscriber.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN newsletter_subscriber.subscribed_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE reset_password_request (id INT NOT NULL, user_id UUID NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)');
        $this->addSql('COMMENT ON COLUMN reset_password_request.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN reset_password_request.requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reset_password_request.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE tenant (id UUID NOT NULL, user_id UUID NOT NULL, gender VARCHAR(255) NOT NULL, activity VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, budget NUMERIC(7, 2) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, longitude VARCHAR(255) NOT NULL, latitude VARCHAR(255) NOT NULL, description TEXT NOT NULL, age INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4E59C462A76ED395 ON tenant (user_id)');
        $this->addSql('COMMENT ON COLUMN tenant.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tenant.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tenant.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE tenant_image (id UUID NOT NULL, tenant_id UUID DEFAULT NULL, mime_type VARCHAR(255) NOT NULL, filename VARCHAR(255) NOT NULL, original_filename VARCHAR(255) NOT NULL, uploaded_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DAADE86B9033212A ON tenant_image (tenant_id)');
        $this->addSql('COMMENT ON COLUMN tenant_image.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tenant_image.tenant_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tenant_image.uploaded_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE user_account (id UUID NOT NULL, email VARCHAR(255) NOT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_of_birth TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL, roles JSON NOT NULL, gender VARCHAR(20) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_253B48AEE7927C74 ON user_account (email)');
        $this->addSql('COMMENT ON COLUMN user_account.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_account.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE user_image (id UUID NOT NULL, user_id UUID NOT NULL, mime_type VARCHAR(255) NOT NULL, filename VARCHAR(255) NOT NULL, original_filename VARCHAR(255) NOT NULL, uploaded_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_27FFFF07A76ED395 ON user_image (user_id)');
        $this->addSql('COMMENT ON COLUMN user_image.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_image.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_image.uploaded_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE chamber ADD CONSTRAINT FK_4359E5AEAD5873E3 FOREIGN KEY (housing_id) REFERENCES housing (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E99EC8D52E FOREIGN KEY (user_one_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E9F59432E1 FOREIGN KEY (user_two_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE housing ADD CONSTRAINT FK_FB8142C3A76ED395 FOREIGN KEY (user_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE housing_image ADD CONSTRAINT FK_163EC29FAD5873E3 FOREIGN KEY (housing_id) REFERENCES housing (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE newsletter_reference ADD CONSTRAINT FK_319C3937808B1AD FOREIGN KEY (subscriber_id) REFERENCES newsletter_subscriber (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tenant ADD CONSTRAINT FK_4E59C462A76ED395 FOREIGN KEY (user_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tenant_image ADD CONSTRAINT FK_DAADE86B9033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_image ADD CONSTRAINT FK_27FFFF07A76ED395 FOREIGN KEY (user_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE french_city_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reset_password_request_id_seq CASCADE');
        $this->addSql('ALTER TABLE chamber DROP CONSTRAINT FK_4359E5AEAD5873E3');
        $this->addSql('ALTER TABLE conversation DROP CONSTRAINT FK_8A8E26E99EC8D52E');
        $this->addSql('ALTER TABLE conversation DROP CONSTRAINT FK_8A8E26E9F59432E1');
        $this->addSql('ALTER TABLE housing DROP CONSTRAINT FK_FB8142C3A76ED395');
        $this->addSql('ALTER TABLE housing_image DROP CONSTRAINT FK_163EC29FAD5873E3');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F9AC0396');
        $this->addSql('ALTER TABLE newsletter_reference DROP CONSTRAINT FK_319C3937808B1AD');
        $this->addSql('ALTER TABLE reset_password_request DROP CONSTRAINT FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE tenant DROP CONSTRAINT FK_4E59C462A76ED395');
        $this->addSql('ALTER TABLE tenant_image DROP CONSTRAINT FK_DAADE86B9033212A');
        $this->addSql('ALTER TABLE user_image DROP CONSTRAINT FK_27FFFF07A76ED395');
        $this->addSql('DROP TABLE chamber');
        $this->addSql('DROP TABLE conversation');
        $this->addSql('DROP TABLE french_city');
        $this->addSql('DROP TABLE housing');
        $this->addSql('DROP TABLE housing_image');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE newsletter_reference');
        $this->addSql('DROP TABLE newsletter_subscriber');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE tenant');
        $this->addSql('DROP TABLE tenant_image');
        $this->addSql('DROP TABLE user_account');
        $this->addSql('DROP TABLE user_image');
    }
}
