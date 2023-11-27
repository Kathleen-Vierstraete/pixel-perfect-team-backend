<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231127154113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact DROP email, DROP first_name, DROP last_name');
        $this->addSql('ALTER TABLE credential CHANGE reset_token reset_token VARCHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE purchase DROP reference, CHANGE date_expected_delivery date_expected_delivery DATETIME DEFAULT NULL, CHANGE date_purchase date_purchase DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact ADD email VARCHAR(100) DEFAULT NULL, ADD first_name VARCHAR(50) DEFAULT NULL, ADD last_name VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE credential CHANGE reset_token reset_token VARCHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE purchase ADD reference VARCHAR(36) NOT NULL, CHANGE date_expected_delivery date_expected_delivery DATETIME NOT NULL, CHANGE date_purchase date_purchase DATETIME NOT NULL');
    }
}
