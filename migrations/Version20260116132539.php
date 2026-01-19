<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260116132539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création des lignes dans League';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('INSERT INTO league (name, crawler_url, code)
            VALUES (\'EuroCup\', \'https://www.basketball-reference.com/international/eurocup\', \'ecp\'),
            (\'EuroLeague\', \'https://www.basketball-reference.com/international/euroleague\', \'elg\')
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('truncate table league restart identity');
    }
}
