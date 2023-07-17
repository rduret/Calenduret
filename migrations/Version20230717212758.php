<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230717212758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE box (id INT AUTO_INCREMENT NOT NULL, calendar_id INT NOT NULL, is_open TINYINT(1) NOT NULL, type VARCHAR(30) NOT NULL, path VARCHAR(255) NOT NULL, coord_x INT NOT NULL, coord_y INT NOT NULL, INDEX IDX_8A9483AA40A2C8 (calendar_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE calendar (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, state VARCHAR(255) NOT NULL, path VARCHAR(255) DEFAULT NULL, INDEX IDX_6EA9A146A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE box ADD CONSTRAINT FK_8A9483AA40A2C8 FOREIGN KEY (calendar_id) REFERENCES calendar (id)');
        $this->addSql('ALTER TABLE calendar ADD CONSTRAINT FK_6EA9A146A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE box DROP FOREIGN KEY FK_8A9483AA40A2C8');
        $this->addSql('ALTER TABLE calendar DROP FOREIGN KEY FK_6EA9A146A76ED395');
        $this->addSql('DROP TABLE box');
        $this->addSql('DROP TABLE calendar');
    }
}
