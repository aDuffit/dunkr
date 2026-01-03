<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260103185436 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE player (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, team VARCHAR(255) DEFAULT NULL, points INTEGER DEFAULT 0 NOT NULL, rebounds INTEGER DEFAULT 0 NOT NULL, assists INTEGER DEFAULT 0 NOT NULL, games INTEGER DEFAULT 0 NOT NULL, minutes_played INTEGER DEFAULT 0 NOT NULL, fields_goals INTEGER DEFAULT 0 NOT NULL, fields_goals_attempts INTEGER DEFAULT 0 NOT NULL, three_fields_goals INTEGER DEFAULT 0 NOT NULL, three_fields_goals_attempts INTEGER DEFAULT 0 NOT NULL, free_throws INTEGER DEFAULT 0 NOT NULL, free_throws_attempts INTEGER DEFAULT 0 NOT NULL, offensive_rebounds INTEGER DEFAULT 0 NOT NULL, defensive_rebounds INTEGER DEFAULT 0 NOT NULL, steals INTEGER DEFAULT 0 NOT NULL, blocks INTEGER DEFAULT 0 NOT NULL, turnovers INTEGER DEFAULT 0 NOT NULL, personal_fouls INTEGER DEFAULT 0 NOT NULL)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE player');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
