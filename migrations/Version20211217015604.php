<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211217015604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY fk_vehicle_id');
        $this->addSql('ALTER TABLE expense ADD description VARCHAR(255) DEFAULT NULL, CHANGE vehicle_id vehicle_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT FK_2D3A8DA6545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (vehicle_id)');
        $this->addSql('ALTER TABLE gas_station DROP FOREIGN KEY fk_expense_id');
        $this->addSql('ALTER TABLE gas_station CHANGE gas_station_id gas_station_id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE expense_id expense_id INT UNSIGNED DEFAULT NULL, CHANGE coordinate coordinate POINT NOT NULL COMMENT \'(DC2Type:point)\'');
        $this->addSql('ALTER TABLE gas_station ADD CONSTRAINT FK_6B3064ACF395DB7B FOREIGN KEY (expense_id) REFERENCES expense (expense_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expense DROP FOREIGN KEY FK_2D3A8DA6545317D1');
        $this->addSql('ALTER TABLE expense DROP description, CHANGE vehicle_id vehicle_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE expense ADD CONSTRAINT fk_vehicle_id FOREIGN KEY (vehicle_id) REFERENCES vehicle (vehicle_id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gas_station DROP FOREIGN KEY FK_6B3064ACF395DB7B');
        $this->addSql('ALTER TABLE gas_station CHANGE gas_station_id gas_station_id INT UNSIGNED NOT NULL, CHANGE expense_id expense_id INT UNSIGNED NOT NULL, CHANGE coordinate coordinate VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE gas_station ADD CONSTRAINT fk_expense_id FOREIGN KEY (expense_id) REFERENCES expense (expense_id) ON UPDATE CASCADE ON DELETE CASCADE');
    }
}
