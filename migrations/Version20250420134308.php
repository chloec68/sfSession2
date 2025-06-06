<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250420134308 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session ADD staff_member_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D444DB03B1 FOREIGN KEY (staff_member_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D044D5D444DB03B1 ON session (staff_member_id)');
        $this->addSql('ALTER TABLE user ADD avatar VARCHAR(20) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D444DB03B1');
        $this->addSql('DROP INDEX IDX_D044D5D444DB03B1 ON session');
        $this->addSql('ALTER TABLE session DROP staff_member_id');
        $this->addSql('ALTER TABLE user DROP avatar');
    }
}
