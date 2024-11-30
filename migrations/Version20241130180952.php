<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241130180952 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE postulation (id INT AUTO_INCREMENT NOT NULL, freelancer_id INT NOT NULL, project_id INT NOT NULL, message VARCHAR(255) NOT NULL, date VARCHAR(255) NOT NULL, INDEX IDX_DA7D4E9B8545BDF5 (freelancer_id), INDEX IDX_DA7D4E9B166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE postulation ADD CONSTRAINT FK_DA7D4E9B8545BDF5 FOREIGN KEY (freelancer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE postulation ADD CONSTRAINT FK_DA7D4E9B166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('CREATE UNIQUE INDEX unique_freelancer_project ON postulacion (freelancer_id, project_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE postulation DROP FOREIGN KEY FK_DA7D4E9B8545BDF5');
        $this->addSql('ALTER TABLE postulation DROP FOREIGN KEY FK_DA7D4E9B166D1F9C');
        $this->addSql('DROP TABLE postulation');
    }
}
