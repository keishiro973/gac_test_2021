<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211217015604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add description field';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE expense ADD description VARCHAR(255) DEFAULT NULL, CHANGE vehicle_id vehicle_id INT UNSIGNED DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE expense DROP description, CHANGE vehicle_id vehicle_id INT UNSIGNED NOT NULL');
    }
}
