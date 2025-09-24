<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * It's for hardcoded available currencies list
 **/
final class Version20250922155223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Insert hardcoded available currencies list';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO currency (iso_code, name) VALUES
            ('USD', 'US Dollar'),
            ('EUR', 'Euro'),
            ('GBP', 'British Pound'),
            ('JPY', 'Japanese Yen'),
            ('AUD', 'Australian Dollar'),
            ('CAD', 'Canadian Dollar'),
            ('CHF', 'Swiss Franc'),
            ('CNY', 'Chinese Yuan'),
            ('SEK', 'Swedish Krona'),
            ('NZD', 'New Zealand Dollar')
        ");

    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM currency WHERE iso_code IN
            ('USD', 'EUR', 'GBP', 'JPY', 'AUD', 'CAD', 'CHF', 'CNY', 'SEK', 'NZD')
        ");
    }
}
