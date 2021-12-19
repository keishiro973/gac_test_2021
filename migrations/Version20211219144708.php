<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211219144708 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gas_station CHANGE gas_station_id gas_station_id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE coordinate coordinate POINT NOT NULL COMMENT \'(DC2Type:point)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gas_station CHANGE gas_station_id gas_station_id INT UNSIGNED NOT NULL, CHANGE coordinate coordinate VARCHAR(255) NOT NULL');
    }
}
