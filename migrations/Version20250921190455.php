<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create exchange_rate table
 */
final class Version20250921190455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create exchange_rate table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE exchange_rate (id SERIAL NOT NULL, iso_code VARCHAR(3) NOT NULL, base_currency_iso VARCHAR(3) NOT NULL, rate DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN exchange_rate.created_at IS \'(DC2Type:datetimetz_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE exchange_rate');
    }
}
