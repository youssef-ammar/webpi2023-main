<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230501060625 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_182BA9BA2048A013');
        $this->addSql('DROP INDEX IDX_182BA9BA2048A013 ON participation');
        $this->addSql('ALTER TABLE participation CHANGE id_ev idEv INT DEFAULT NULL');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_182BA9BA1CDC6B20 FOREIGN KEY (idEv) REFERENCES evenement (id)');
        $this->addSql('CREATE INDEX IDX_182BA9BA1CDC6B20 ON participation (idEv)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Participation DROP FOREIGN KEY FK_182BA9BA1CDC6B20');
        $this->addSql('DROP INDEX IDX_182BA9BA1CDC6B20 ON Participation');
        $this->addSql('ALTER TABLE Participation CHANGE idEv id_ev INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Participation ADD CONSTRAINT FK_182BA9BA2048A013 FOREIGN KEY (id_ev) REFERENCES evenement (id)');
        $this->addSql('CREATE INDEX IDX_182BA9BA2048A013 ON Participation (id_ev)');
    }
}
