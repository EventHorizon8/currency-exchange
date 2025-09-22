<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create currency table
 */
final class Version20250921185608 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create currency table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE currency (id SERIAL NOT NULL, iso_code VARCHAR(3) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE currency');
    }
}
