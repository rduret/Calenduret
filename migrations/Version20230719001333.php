<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230719001333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE box (id INT AUTO_INCREMENT NOT NULL, calendar_id INT NOT NULL, model_box_id INT NOT NULL, is_open TINYINT(1) NOT NULL, INDEX IDX_8A9483AA40A2C8 (calendar_id), INDEX IDX_8A9483AFBF05FA0 (model_box_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE calendar (id INT AUTO_INCREMENT NOT NULL, model_calendar_id INT NOT NULL, uuid VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_6EA9A14664429B24 (model_calendar_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE model_box (id INT AUTO_INCREMENT NOT NULL, model_calendar_id INT NOT NULL, path VARCHAR(255) NOT NULL, type VARCHAR(30) NOT NULL, coord_x INT NOT NULL, coord_y INT NOT NULL, INDEX IDX_5D6A22C564429B24 (model_calendar_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE model_calendar (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, title VARCHAR(255) NOT NULL, state VARCHAR(30) NOT NULL, path VARCHAR(255) DEFAULT NULL, uuid VARCHAR(255) NOT NULL, INDEX IDX_A473F0A5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE box ADD CONSTRAINT FK_8A9483AA40A2C8 FOREIGN KEY (calendar_id) REFERENCES calendar (id)');
        $this->addSql('ALTER TABLE box ADD CONSTRAINT FK_8A9483AFBF05FA0 FOREIGN KEY (model_box_id) REFERENCES model_box (id)');
        $this->addSql('ALTER TABLE calendar ADD CONSTRAINT FK_6EA9A14664429B24 FOREIGN KEY (model_calendar_id) REFERENCES model_calendar (id)');
        $this->addSql('ALTER TABLE model_box ADD CONSTRAINT FK_5D6A22C564429B24 FOREIGN KEY (model_calendar_id) REFERENCES model_calendar (id)');
        $this->addSql('ALTER TABLE model_calendar ADD CONSTRAINT FK_A473F0A5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE box DROP FOREIGN KEY FK_8A9483AA40A2C8');
        $this->addSql('ALTER TABLE box DROP FOREIGN KEY FK_8A9483AFBF05FA0');
        $this->addSql('ALTER TABLE calendar DROP FOREIGN KEY FK_6EA9A14664429B24');
        $this->addSql('ALTER TABLE model_box DROP FOREIGN KEY FK_5D6A22C564429B24');
        $this->addSql('ALTER TABLE model_calendar DROP FOREIGN KEY FK_A473F0A5A76ED395');
        $this->addSql('DROP TABLE box');
        $this->addSql('DROP TABLE calendar');
        $this->addSql('DROP TABLE model_box');
        $this->addSql('DROP TABLE model_calendar');
    }
}
