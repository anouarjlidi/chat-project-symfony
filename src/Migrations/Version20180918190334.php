<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180918190334 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE web_site (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', admin_user_id INT DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, is_online TINYINT(1) DEFAULT NULL, has_admin_chat TINYINT(1) DEFAULT NULL, has_private_chat TINYINT(1) DEFAULT NULL, is_wordpress TINYINT(1) DEFAULT NULL, admin_temp_user VARCHAR(255) DEFAULT NULL, installed TINYINT(1) NOT NULL, source_code LONGTEXT DEFAULT NULL, css_admin_chat LONGTEXT NOT NULL, template_admin_chat LONGTEXT NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, deletedAt DATETIME DEFAULT NULL, INDEX IDX_AD4104116352511C (admin_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chat_room (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', web_site_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', chat_type VARCHAR(10) NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, deletedAt DATETIME DEFAULT NULL, INDEX IDX_D403CCDA1E12B8D8 (web_site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chat_room_foreign_user (chat_room_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', foreign_user_id INT NOT NULL, INDEX IDX_6D53C0E31819BCFA (chat_room_id), INDEX IDX_6D53C0E33D007BFD (foreign_user_id), PRIMARY KEY(chat_room_id, foreign_user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE foreign_user (id INT AUTO_INCREMENT NOT NULL, web_site_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', user_id VARCHAR(36) NOT NULL, user_name VARCHAR(36) DEFAULT NULL, user_image VARCHAR(255) DEFAULT NULL, INDEX IDX_3E010AA91E12B8D8 (web_site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ext_translations (id INT AUTO_INCREMENT NOT NULL, object_class VARCHAR(191) NOT NULL, locale VARCHAR(8) NOT NULL, field VARCHAR(32) NOT NULL, foreign_key VARCHAR(64) NOT NULL, content LONGTEXT DEFAULT NULL, INDEX translations_lookup_idx (locale, object_class, foreign_key), UNIQUE INDEX lookup_unique_idx (locale, object_class, field, foreign_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', locale VARCHAR(2) DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, deletedAt DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D64992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_8D93D649A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_8D93D649C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_web_site (user_id INT NOT NULL, web_site_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_4DCCE011A76ED395 (user_id), INDEX IDX_4DCCE0111E12B8D8 (web_site_id), PRIMARY KEY(user_id, web_site_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE web_site ADD CONSTRAINT FK_AD4104116352511C FOREIGN KEY (admin_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE chat_room ADD CONSTRAINT FK_D403CCDA1E12B8D8 FOREIGN KEY (web_site_id) REFERENCES web_site (id)');
        $this->addSql('ALTER TABLE chat_room_foreign_user ADD CONSTRAINT FK_6D53C0E31819BCFA FOREIGN KEY (chat_room_id) REFERENCES chat_room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chat_room_foreign_user ADD CONSTRAINT FK_6D53C0E33D007BFD FOREIGN KEY (foreign_user_id) REFERENCES foreign_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE foreign_user ADD CONSTRAINT FK_3E010AA91E12B8D8 FOREIGN KEY (web_site_id) REFERENCES web_site (id)');
        $this->addSql('ALTER TABLE user_web_site ADD CONSTRAINT FK_4DCCE011A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_web_site ADD CONSTRAINT FK_4DCCE0111E12B8D8 FOREIGN KEY (web_site_id) REFERENCES web_site (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE chat_room DROP FOREIGN KEY FK_D403CCDA1E12B8D8');
        $this->addSql('ALTER TABLE foreign_user DROP FOREIGN KEY FK_3E010AA91E12B8D8');
        $this->addSql('ALTER TABLE user_web_site DROP FOREIGN KEY FK_4DCCE0111E12B8D8');
        $this->addSql('ALTER TABLE chat_room_foreign_user DROP FOREIGN KEY FK_6D53C0E31819BCFA');
        $this->addSql('ALTER TABLE chat_room_foreign_user DROP FOREIGN KEY FK_6D53C0E33D007BFD');
        $this->addSql('ALTER TABLE web_site DROP FOREIGN KEY FK_AD4104116352511C');
        $this->addSql('ALTER TABLE user_web_site DROP FOREIGN KEY FK_4DCCE011A76ED395');
        $this->addSql('DROP TABLE web_site');
        $this->addSql('DROP TABLE chat_room');
        $this->addSql('DROP TABLE chat_room_foreign_user');
        $this->addSql('DROP TABLE foreign_user');
        $this->addSql('DROP TABLE ext_translations');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_web_site');
    }
}
