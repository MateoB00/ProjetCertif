<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220613140749 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abonnement ADD coach_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE abonnement ADD CONSTRAINT FK_351268BB3C105691 FOREIGN KEY (coach_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_351268BB3C105691 ON abonnement (coach_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abonnement DROP FOREIGN KEY FK_351268BB3C105691');
        $this->addSql('DROP INDEX IDX_351268BB3C105691 ON abonnement');
        $this->addSql('ALTER TABLE abonnement DROP coach_id');
    }
}
