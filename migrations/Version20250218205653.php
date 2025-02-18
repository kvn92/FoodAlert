<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218205653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, recette_id INT NOT NULL, commentaire LONGTEXT NOT NULL, is_active TINYINT(1) DEFAULT 0 NOT NULL, create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_67F068BCA76ED395 (user_id), INDEX IDX_67F068BC89312FE9 (recette_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE like_recette (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, recette_id INT NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, INDEX IDX_1A3629B3A76ED395 (user_id), INDEX IDX_1A3629B389312FE9 (recette_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recette (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, titre VARCHAR(255) NOT NULL, preparation LONGTEXT NOT NULL, photo VARCHAR(255) NOT NULL, is_active TINYINT(1) DEFAULT 0 NOT NULL, create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_49BB6390A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sauvergarde_recette (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, recette_id INT NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, INDEX IDX_9F4CCEF5A76ED395 (user_id), INDEX IDX_9F4CCEF589312FE9 (recette_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, pseudo VARCHAR(30) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, photo VARCHAR(255) DEFAULT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), UNIQUE INDEX UNIQ_IDENTIFIER_PSEUDO (pseudo), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_follow (id INT AUTO_INCREMENT NOT NULL, follower_id INT NOT NULL, following_id INT NOT NULL, create_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_D665F4DAC24F853 (follower_id), INDEX IDX_D665F4D1816E3A3 (following_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC89312FE9 FOREIGN KEY (recette_id) REFERENCES recette (id)');
        $this->addSql('ALTER TABLE like_recette ADD CONSTRAINT FK_1A3629B3A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE like_recette ADD CONSTRAINT FK_1A3629B389312FE9 FOREIGN KEY (recette_id) REFERENCES recette (id)');
        $this->addSql('ALTER TABLE recette ADD CONSTRAINT FK_49BB6390A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE sauvergarde_recette ADD CONSTRAINT FK_9F4CCEF5A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE sauvergarde_recette ADD CONSTRAINT FK_9F4CCEF589312FE9 FOREIGN KEY (recette_id) REFERENCES recette (id)');
        $this->addSql('ALTER TABLE user_follow ADD CONSTRAINT FK_D665F4DAC24F853 FOREIGN KEY (follower_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_follow ADD CONSTRAINT FK_D665F4D1816E3A3 FOREIGN KEY (following_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCA76ED395');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC89312FE9');
        $this->addSql('ALTER TABLE like_recette DROP FOREIGN KEY FK_1A3629B3A76ED395');
        $this->addSql('ALTER TABLE like_recette DROP FOREIGN KEY FK_1A3629B389312FE9');
        $this->addSql('ALTER TABLE recette DROP FOREIGN KEY FK_49BB6390A76ED395');
        $this->addSql('ALTER TABLE sauvergarde_recette DROP FOREIGN KEY FK_9F4CCEF5A76ED395');
        $this->addSql('ALTER TABLE sauvergarde_recette DROP FOREIGN KEY FK_9F4CCEF589312FE9');
        $this->addSql('ALTER TABLE user_follow DROP FOREIGN KEY FK_D665F4DAC24F853');
        $this->addSql('ALTER TABLE user_follow DROP FOREIGN KEY FK_D665F4D1816E3A3');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE like_recette');
        $this->addSql('DROP TABLE recette');
        $this->addSql('DROP TABLE sauvergarde_recette');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_follow');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
