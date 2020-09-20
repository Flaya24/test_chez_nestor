<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200920193935 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B3883CD9A');
        $this->addSql('DROP INDEX IDX_729F519B3883CD9A ON room');
        $this->addSql('ALTER TABLE room CHANGE apartment_id_id apartment_id INT NOT NULL');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B176DFE85 FOREIGN KEY (apartment_id) REFERENCES apartment (id)');
        $this->addSql('CREATE INDEX IDX_729F519B176DFE85 ON room (apartment_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B176DFE85');
        $this->addSql('DROP INDEX IDX_729F519B176DFE85 ON room');
        $this->addSql('ALTER TABLE room CHANGE apartment_id apartment_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B3883CD9A FOREIGN KEY (apartment_id_id) REFERENCES apartment (id)');
        $this->addSql('CREATE INDEX IDX_729F519B3883CD9A ON room (apartment_id_id)');
    }
}
