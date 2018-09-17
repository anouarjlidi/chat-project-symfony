<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180917182915 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE web_site ADD CONSTRAINT FK_AD4104116352511C FOREIGN KEY (admin_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE chat_room ADD user_web_site_for_admin_user_id VARCHAR(36) NOT NULL, CHANGE user_web_site_for_admin_id user_web_site_for_admin_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE chat_room ADD CONSTRAINT FK_D403CCDA9A60C7BB FOREIGN KEY (user_web_site_for_admin_id) REFERENCES foreign_user_web_site (id)');
        $this->addSql('ALTER TABLE user_web_site ADD CONSTRAINT FK_4DCCE011A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_web_site ADD CONSTRAINT FK_4DCCE0111E12B8D8 FOREIGN KEY (web_site_id) REFERENCES web_site (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE chat_room DROP FOREIGN KEY FK_D403CCDA9A60C7BB');
        $this->addSql('ALTER TABLE chat_room DROP user_web_site_for_admin_user_id, CHANGE user_web_site_for_admin_id user_web_site_for_admin_id VARCHAR(36) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE user_web_site DROP FOREIGN KEY FK_4DCCE011A76ED395');
        $this->addSql('ALTER TABLE user_web_site DROP FOREIGN KEY FK_4DCCE0111E12B8D8');
        $this->addSql('ALTER TABLE web_site DROP FOREIGN KEY FK_AD4104116352511C');
    }
}
