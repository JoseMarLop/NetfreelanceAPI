<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241117161125 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_abilities (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, text VARCHAR(255) NOT NULL, INDEX IDX_B8D80040A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_links (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, text VARCHAR(255) NOT NULL, icon VARCHAR(25) DEFAULT NULL, INDEX IDX_33405A40A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_abilities ADD CONSTRAINT FK_B8D80040A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_links ADD CONSTRAINT FK_33405A40A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD rating DOUBLE PRECISION DEFAULT NULL, ADD address VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_abilities DROP FOREIGN KEY FK_B8D80040A76ED395');
        $this->addSql('ALTER TABLE user_links DROP FOREIGN KEY FK_33405A40A76ED395');
        $this->addSql('DROP TABLE user_abilities');
        $this->addSql('DROP TABLE user_links');
        $this->addSql('ALTER TABLE user DROP rating, DROP address');
    }
}
