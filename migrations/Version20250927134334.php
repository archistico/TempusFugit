<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250927134334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "action" (id BLOB NOT NULL --(DC2Type:uuid)
        , project_id BLOB NOT NULL --(DC2Type:uuid)
        , type_id BLOB NOT NULL --(DC2Type:uuid)
        , status_id BLOB NOT NULL --(DC2Type:uuid)
        , titolo VARCHAR(255) NOT NULL, descrizione VARCHAR(255) DEFAULT NULL, stima_min INTEGER NOT NULL, deadline DATE DEFAULT NULL --(DC2Type:date_immutable)
        , fatturabile BOOLEAN NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , PRIMARY KEY(id), CONSTRAINT FK_47CC8C92166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_47CC8C92C54C8C93 FOREIGN KEY (type_id) REFERENCES action_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_47CC8C926BF700BD FOREIGN KEY (status_id) REFERENCES action_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_47CC8C92166D1F9C ON "action" (project_id)');
        $this->addSql('CREATE INDEX IDX_47CC8C92C54C8C93 ON "action" (type_id)');
        $this->addSql('CREATE INDEX IDX_47CC8C926BF700BD ON "action" (status_id)');
        $this->addSql('CREATE TABLE action_status (id BLOB NOT NULL --(DC2Type:uuid)
        , descrizione VARCHAR(255) NOT NULL, ordine INTEGER NOT NULL, chiusura BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE action_type (id BLOB NOT NULL --(DC2Type:uuid)
        , descrizione VARCHAR(255) NOT NULL, colore VARCHAR(255) DEFAULT NULL, icona VARCHAR(255) DEFAULT NULL, fatturabile_default BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE client (id BLOB NOT NULL --(DC2Type:uuid)
        , denominazione VARCHAR(255) NOT NULL, piva VARCHAR(255) DEFAULT NULL, cf VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, telefono VARCHAR(255) DEFAULT NULL, via VARCHAR(255) DEFAULT NULL, cap VARCHAR(255) DEFAULT NULL, citta VARCHAR(255) DEFAULT NULL, note VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE communication (id BLOB NOT NULL --(DC2Type:uuid)
        , project_id BLOB NOT NULL --(DC2Type:uuid)
        , client_id BLOB NOT NULL --(DC2Type:uuid)
        , data DATE NOT NULL --(DC2Type:date_immutable)
        , comunicazione VARCHAR(255) NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_F9AFB5EB166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F9AFB5EB19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F9AFB5EB166D1F9C ON communication (project_id)');
        $this->addSql('CREATE INDEX IDX_F9AFB5EB19EB6921 ON communication (client_id)');
        $this->addSql('CREATE TABLE ledger_movement (id BLOB NOT NULL --(DC2Type:uuid)
        , project_id BLOB NOT NULL --(DC2Type:uuid)
        , data DATE DEFAULT NULL --(DC2Type:date_immutable)
        , tipo VARCHAR(255) NOT NULL, categoria VARCHAR(255) NOT NULL, importo NUMERIC(12, 2) NOT NULL, descrizione VARCHAR(255) DEFAULT NULL, nota VARCHAR(255) DEFAULT NULL, iva_percent NUMERIC(5, 2) NOT NULL, pagato BOOLEAN NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_A5CD900D166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_A5CD900D166D1F9C ON ledger_movement (project_id)');
        $this->addSql('CREATE TABLE project (id BLOB NOT NULL --(DC2Type:uuid)
        , client_id BLOB NOT NULL --(DC2Type:uuid)
        , type_id BLOB NOT NULL --(DC2Type:uuid)
        , titolo VARCHAR(255) NOT NULL, descrizione VARCHAR(255) DEFAULT NULL, tipologia_fatturazione VARCHAR(255) NOT NULL, note VARCHAR(255) DEFAULT NULL, data_inizio DATE DEFAULT NULL --(DC2Type:date_immutable)
        , data_fine_stimata DATE DEFAULT NULL --(DC2Type:date_immutable)
        , data_fine_reale DATE DEFAULT NULL --(DC2Type:date_immutable)
        , stato VARCHAR(255) NOT NULL, path_progetto VARCHAR(255) DEFAULT NULL, percent_avanz DOUBLE PRECISION NOT NULL, importo_preventivo NUMERIC(12, 2) NOT NULL, condizioni_pagamento VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , PRIMARY KEY(id), CONSTRAINT FK_2FB3D0EE19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2FB3D0EEC54C8C93 FOREIGN KEY (type_id) REFERENCES project_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE19EB6921 ON project (client_id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EEC54C8C93 ON project (type_id)');
        $this->addSql('CREATE TABLE project_type (id BLOB NOT NULL --(DC2Type:uuid)
        , descrizione VARCHAR(255) NOT NULL, costo_orario_default NUMERIC(10, 2) DEFAULT NULL, version INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE project_type_action_template (id BLOB NOT NULL --(DC2Type:uuid)
        , project_type_id BLOB DEFAULT NULL --(DC2Type:uuid)
        , action_type_id BLOB DEFAULT NULL --(DC2Type:uuid)
        , status_id BLOB DEFAULT NULL --(DC2Type:uuid)
        , titolo VARCHAR(255) NOT NULL, descrizione VARCHAR(255) DEFAULT NULL, stima_min INTEGER NOT NULL, ordine INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_9160C83E535280F6 FOREIGN KEY (project_type_id) REFERENCES project_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9160C83E1FEE0472 FOREIGN KEY (action_type_id) REFERENCES action_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9160C83E6BF700BD FOREIGN KEY (status_id) REFERENCES action_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_9160C83E535280F6 ON project_type_action_template (project_type_id)');
        $this->addSql('CREATE INDEX IDX_9160C83E1FEE0472 ON project_type_action_template (action_type_id)');
        $this->addSql('CREATE INDEX IDX_9160C83E6BF700BD ON project_type_action_template (status_id)');
        $this->addSql('CREATE TABLE setting (id BLOB NOT NULL --(DC2Type:uuid)
        , chiave VARCHAR(255) NOT NULL, valore VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE time_entry (id BLOB NOT NULL --(DC2Type:uuid)
        , project_id BLOB NOT NULL --(DC2Type:uuid)
        , project_action_id BLOB NOT NULL --(DC2Type:uuid)
        , start_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , end_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , durata_min INTEGER DEFAULT NULL, descrizione VARCHAR(255) DEFAULT NULL, billable BOOLEAN DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT FK_6E537C0C166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_6E537C0C8C0EB5E2 FOREIGN KEY (project_action_id) REFERENCES "action" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_6E537C0C166D1F9C ON time_entry (project_id)');
        $this->addSql('CREATE INDEX IDX_6E537C0C8C0EB5E2 ON time_entry (project_action_id)');
        $this->addSql('CREATE TABLE user (id BLOB NOT NULL --(DC2Type:uuid)
        , cliente_id BLOB DEFAULT NULL --(DC2Type:uuid)
        , email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, is_active BOOLEAN NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_8D93D649DE734E51 FOREIGN KEY (cliente_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_8D93D649DE734E51 ON user (cliente_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE "action"');
        $this->addSql('DROP TABLE action_status');
        $this->addSql('DROP TABLE action_type');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE communication');
        $this->addSql('DROP TABLE ledger_movement');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_type');
        $this->addSql('DROP TABLE project_type_action_template');
        $this->addSql('DROP TABLE setting');
        $this->addSql('DROP TABLE time_entry');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
