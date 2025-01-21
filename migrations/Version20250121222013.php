<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250121222013 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE answer (id UUID NOT NULL, question_id UUID DEFAULT NULL, content TEXT NOT NULL, correct BOOLEAN NOT NULL, position INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DADD4A251E27F6BF ON answer (question_id)');
        $this->addSql('COMMENT ON COLUMN answer.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN answer.question_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE app_setting (id UUID NOT NULL, key VARCHAR(255) NOT NULL, value JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_722938D58A90ABA9 ON app_setting (key)');
        $this->addSql('COMMENT ON COLUMN app_setting.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE module (id UUID NOT NULL, name VARCHAR(255) NOT NULL, language VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN module.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE module_question (module_id UUID NOT NULL, question_id UUID NOT NULL, PRIMARY KEY(module_id, question_id))');
        $this->addSql('CREATE INDEX IDX_D2379AB0AFC2B591 ON module_question (module_id)');
        $this->addSql('CREATE INDEX IDX_D2379AB01E27F6BF ON module_question (question_id)');
        $this->addSql('COMMENT ON COLUMN module_question.module_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN module_question.question_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE question (id UUID NOT NULL, content TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN question.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE security_user (id UUID NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON security_user (email)');
        $this->addSql('COMMENT ON COLUMN security_user.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE test (id UUID NOT NULL, module_id UUID DEFAULT NULL, expiration TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, submission TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, workplace VARCHAR(255) DEFAULT NULL, date_of_birth DATE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D87F7E0CAFC2B591 ON test (module_id)');
        $this->addSql('COMMENT ON COLUMN test.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN test.module_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE video (id UUID NOT NULL, video_name VARCHAR(255) NOT NULL, size VARCHAR(255) NOT NULL, mime_type VARCHAR(255) NOT NULL, original_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN video.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE video_module (video_id UUID NOT NULL, module_id UUID NOT NULL, PRIMARY KEY(video_id, module_id))');
        $this->addSql('CREATE INDEX IDX_347386E529C1004E ON video_module (video_id)');
        $this->addSql('CREATE INDEX IDX_347386E5AFC2B591 ON video_module (module_id)');
        $this->addSql('COMMENT ON COLUMN video_module.video_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN video_module.module_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A251E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE module_question ADD CONSTRAINT FK_D2379AB0AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE module_question ADD CONSTRAINT FK_D2379AB01E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE test ADD CONSTRAINT FK_D87F7E0CAFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video_module ADD CONSTRAINT FK_347386E529C1004E FOREIGN KEY (video_id) REFERENCES video (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video_module ADD CONSTRAINT FK_347386E5AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE answer DROP CONSTRAINT FK_DADD4A251E27F6BF');
        $this->addSql('ALTER TABLE module_question DROP CONSTRAINT FK_D2379AB0AFC2B591');
        $this->addSql('ALTER TABLE module_question DROP CONSTRAINT FK_D2379AB01E27F6BF');
        $this->addSql('ALTER TABLE test DROP CONSTRAINT FK_D87F7E0CAFC2B591');
        $this->addSql('ALTER TABLE video_module DROP CONSTRAINT FK_347386E529C1004E');
        $this->addSql('ALTER TABLE video_module DROP CONSTRAINT FK_347386E5AFC2B591');
        $this->addSql('DROP TABLE answer');
        $this->addSql('DROP TABLE app_setting');
        $this->addSql('DROP TABLE module');
        $this->addSql('DROP TABLE module_question');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE security_user');
        $this->addSql('DROP TABLE test');
        $this->addSql('DROP TABLE video');
        $this->addSql('DROP TABLE video_module');
    }
}
