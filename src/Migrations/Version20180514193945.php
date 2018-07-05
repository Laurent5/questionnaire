<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180514193945 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE reponses (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, texte VARCHAR(255) NOT NULL, discr VARCHAR(255) NOT NULL, INDEX IDX_1E512EC61E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponses_ouverte (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponses_ferme (id INT NOT NULL, question_si_selectionne_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_3027282F606F249F (question_si_selectionne_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE thematique (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, thematique_id INT DEFAULT NULL, question VARCHAR(255) NOT NULL, ordre INT DEFAULT NULL, INDEX IDX_B6F7494E476556AF (thematique_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reponses ADD CONSTRAINT FK_1E512EC61E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE reponses_ouverte ADD CONSTRAINT FK_AFE8E7F8BF396750 FOREIGN KEY (id) REFERENCES reponses (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reponses_ferme ADD CONSTRAINT FK_3027282F606F249F FOREIGN KEY (question_si_selectionne_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE reponses_ferme ADD CONSTRAINT FK_3027282FBF396750 FOREIGN KEY (id) REFERENCES reponses (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E476556AF FOREIGN KEY (thematique_id) REFERENCES thematique (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reponses_ouverte DROP FOREIGN KEY FK_AFE8E7F8BF396750');
        $this->addSql('ALTER TABLE reponses_ferme DROP FOREIGN KEY FK_3027282FBF396750');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E476556AF');
        $this->addSql('ALTER TABLE reponses DROP FOREIGN KEY FK_1E512EC61E27F6BF');
        $this->addSql('ALTER TABLE reponses_ferme DROP FOREIGN KEY FK_3027282F606F249F');
        $this->addSql('DROP TABLE reponses');
        $this->addSql('DROP TABLE reponses_ouverte');
        $this->addSql('DROP TABLE reponses_ferme');
        $this->addSql('DROP TABLE thematique');
        $this->addSql('DROP TABLE question');
    }
}
