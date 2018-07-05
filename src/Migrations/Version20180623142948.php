<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180623142948 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE question_prerequis (id INT AUTO_INCREMENT NOT NULL, reponse_id INT DEFAULT NULL, question_id INT DEFAULT NULL, INDEX IDX_C336AF92CF18BB82 (reponse_id), INDEX IDX_C336AF921E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE question_prerequis ADD CONSTRAINT FK_C336AF92CF18BB82 FOREIGN KEY (reponse_id) REFERENCES reponses_ferme (id)');
        $this->addSql('ALTER TABLE question_prerequis ADD CONSTRAINT FK_C336AF921E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('DROP TABLE question_reponses_ferme');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE question_reponses_ferme (question_id INT NOT NULL, reponses_ferme_id INT NOT NULL, INDEX IDX_D807BDD61E27F6BF (question_id), INDEX IDX_D807BDD6AD07DF1C (reponses_ferme_id), PRIMARY KEY(question_id, reponses_ferme_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE question_reponses_ferme ADD CONSTRAINT FK_D807BDD61E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE question_reponses_ferme ADD CONSTRAINT FK_D807BDD6AD07DF1C FOREIGN KEY (reponses_ferme_id) REFERENCES reponses_ferme (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE question_prerequis');
    }
}
