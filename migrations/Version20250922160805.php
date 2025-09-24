<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Unique index on iso_code in currency table
 */
final class Version20250922160805 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'UNIQUE index on iso_code in currency table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6956883F62B6A45E ON currency (iso_code)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_6956883F62B6A45E');
    }
}
