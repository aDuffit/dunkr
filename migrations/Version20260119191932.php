<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260119191932 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player ADD height DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE player ADD birthdate DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE player ADD position TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE player ADD url_entry_point VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE player DROP height');
        $this->addSql('ALTER TABLE player DROP birthdate');
        $this->addSql('ALTER TABLE player DROP position');
        $this->addSql('ALTER TABLE player DROP url_entry_point');
    }
}
