<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210709104057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE container ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE container ADD CONSTRAINT FK_C7A2EC1BA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_C7A2EC1BA76ED395 ON container (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE container DROP FOREIGN KEY FK_C7A2EC1BA76ED395');
        $this->addSql('DROP INDEX IDX_C7A2EC1BA76ED395 ON container');
        $this->addSql('ALTER TABLE container DROP user_id');
    }
}
