<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180617143917 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE reponses_fournies_individuelles (id INT AUTO_INCREMENT NOT NULL, questions_id INT DEFAULT NULL, questionnaire_id INT DEFAULT NULL, discr VARCHAR(255) NOT NULL, INDEX IDX_1AE53013BCB134CE (questions_id), INDEX IDX_1AE53013CE07E8FF (questionnaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponses_fournies_individuelles_ouverte (id INT NOT NULL, valeur LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponses_fournies_individuelles_ferme (id INT NOT NULL, reponses_ferme_id INT DEFAULT NULL, INDEX IDX_381DFD41AD07DF1C (reponses_ferme_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponses_fournies (id INT AUTO_INCREMENT NOT NULL, thematique_id INT DEFAULT NULL, repondant_id VARCHAR(255) NOT NULL, repondant_token VARCHAR(255) NOT NULL, questionnaire_id VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_2E436451476556AF (thematique_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reponses_fournies_individuelles ADD CONSTRAINT FK_1AE53013BCB134CE FOREIGN KEY (questions_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE reponses_fournies_individuelles ADD CONSTRAINT FK_1AE53013CE07E8FF FOREIGN KEY (questionnaire_id) REFERENCES reponses_fournies (id)');
        $this->addSql('ALTER TABLE reponses_fournies_individuelles_ouverte ADD CONSTRAINT FK_A22F8FB0BF396750 FOREIGN KEY (id) REFERENCES reponses_fournies_individuelles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reponses_fournies_individuelles_ferme ADD CONSTRAINT FK_381DFD41AD07DF1C FOREIGN KEY (reponses_ferme_id) REFERENCES reponses_ferme (id)');
        $this->addSql('ALTER TABLE reponses_fournies_individuelles_ferme ADD CONSTRAINT FK_381DFD41BF396750 FOREIGN KEY (id) REFERENCES reponses_fournies_individuelles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reponses_fournies ADD CONSTRAINT FK_2E436451476556AF FOREIGN KEY (thematique_id) REFERENCES thematique (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reponses_fournies_individuelles_ouverte DROP FOREIGN KEY FK_A22F8FB0BF396750');
        $this->addSql('ALTER TABLE reponses_fournies_individuelles_ferme DROP FOREIGN KEY FK_381DFD41BF396750');
        $this->addSql('ALTER TABLE reponses_fournies_individuelles DROP FOREIGN KEY FK_1AE53013CE07E8FF');
        $this->addSql('DROP TABLE reponses_fournies_individuelles');
        $this->addSql('DROP TABLE reponses_fournies_individuelles_ouverte');
        $this->addSql('DROP TABLE reponses_fournies_individuelles_ferme');
        $this->addSql('DROP TABLE reponses_fournies');
    }
}
