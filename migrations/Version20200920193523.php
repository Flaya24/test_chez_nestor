<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200920193523 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495535F83FFC');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955DC2902E0');
        $this->addSql('DROP INDEX IDX_42C8495535F83FFC ON reservation');
        $this->addSql('DROP INDEX IDX_42C84955DC2902E0 ON reservation');
        $this->addSql('ALTER TABLE reservation ADD client_id INT NOT NULL, ADD room_id INT NOT NULL, DROP client_id_id, DROP room_id_id');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495519EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495554177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('CREATE INDEX IDX_42C8495519EB6921 ON reservation (client_id)');
        $this->addSql('CREATE INDEX IDX_42C8495554177093 ON reservation (room_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495519EB6921');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495554177093');
        $this->addSql('DROP INDEX IDX_42C8495519EB6921 ON reservation');
        $this->addSql('DROP INDEX IDX_42C8495554177093 ON reservation');
        $this->addSql('ALTER TABLE reservation ADD client_id_id INT NOT NULL, ADD room_id_id INT NOT NULL, DROP client_id, DROP room_id');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495535F83FFC FOREIGN KEY (room_id_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955DC2902E0 FOREIGN KEY (client_id_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_42C8495535F83FFC ON reservation (room_id_id)');
        $this->addSql('CREATE INDEX IDX_42C84955DC2902E0 ON reservation (client_id_id)');
    }
}
