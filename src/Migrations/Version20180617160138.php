<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180617160138 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reponses_fournies DROP FOREIGN KEY FK_2E436451476556AF');
        $this->addSql('DROP INDEX IDX_2E436451476556AF ON reponses_fournies');
        $this->addSql('ALTER TABLE reponses_fournies DROP thematique_id, DROP repondant_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reponses_fournies ADD thematique_id INT DEFAULT NULL, ADD repondant_id VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE reponses_fournies ADD CONSTRAINT FK_2E436451476556AF FOREIGN KEY (thematique_id) REFERENCES thematique (id)');
        $this->addSql('CREATE INDEX IDX_2E436451476556AF ON reponses_fournies (thematique_id)');
    }
}
