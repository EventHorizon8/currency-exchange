<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add index on iso_code in exchange_rate table
 */
final class Version20250924161520 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add index on iso_code in exchange_rate table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX exchange_rate_iso_code_idx ON exchange_rate (iso_code)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX exchange_rate_iso_code_idx');
    }
}
