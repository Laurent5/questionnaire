<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180620141418 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reponses_ferme DROP FOREIGN KEY FK_3027282F606F249F');
        $this->addSql('DROP INDEX UNIQ_3027282F606F249F ON reponses_ferme');
        $this->addSql('ALTER TABLE reponses_ferme DROP question_si_selectionne_id');
        $this->addSql('ALTER TABLE question ADD reponse_pre_requise_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E61A66BD1 FOREIGN KEY (reponse_pre_requise_id) REFERENCES reponses_ferme (id)');
        $this->addSql('CREATE INDEX IDX_B6F7494E61A66BD1 ON question (reponse_pre_requise_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E61A66BD1');
        $this->addSql('DROP INDEX IDX_B6F7494E61A66BD1 ON question');
        $this->addSql('ALTER TABLE question DROP reponse_pre_requise_id');
        $this->addSql('ALTER TABLE reponses_ferme ADD question_si_selectionne_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reponses_ferme ADD CONSTRAINT FK_3027282F606F249F FOREIGN KEY (question_si_selectionne_id) REFERENCES question (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3027282F606F249F ON reponses_ferme (question_si_selectionne_id)');
    }
}
