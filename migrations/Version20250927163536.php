<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250927163536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE communication ADD COLUMN tipologia VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__communication AS SELECT id, project_id, client_id, data, comunicazione FROM communication');
        $this->addSql('DROP TABLE communication');
        $this->addSql('CREATE TABLE communication (id VARCHAR(36) NOT NULL, project_id VARCHAR(36) NOT NULL, client_id VARCHAR(36) NOT NULL, data DATE NOT NULL --(DC2Type:date_immutable)
        , comunicazione VARCHAR(255) NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_F9AFB5EB166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F9AFB5EB19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO communication (id, project_id, client_id, data, comunicazione) SELECT id, project_id, client_id, data, comunicazione FROM __temp__communication');
        $this->addSql('DROP TABLE __temp__communication');
        $this->addSql('CREATE INDEX IDX_F9AFB5EB166D1F9C ON communication (project_id)');
        $this->addSql('CREATE INDEX IDX_F9AFB5EB19EB6921 ON communication (client_id)');
    }
}
