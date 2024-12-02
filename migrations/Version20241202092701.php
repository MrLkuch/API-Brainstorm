<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241202092701 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chatroom (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, _user_id INT NOT NULL, _chatroom_id INT NOT NULL, content LONGTEXT NOT NULL, sent_at DATETIME NOT NULL, INDEX IDX_B6BD307FD32632E8 (_user_id), INDEX IDX_B6BD307F944512CF (_chatroom_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_chatroom (id INT AUTO_INCREMENT NOT NULL, _user_id INT NOT NULL, _chatroom_id INT NOT NULL, last_read DATETIME DEFAULT NULL, INDEX IDX_FFB38AC4D32632E8 (_user_id), INDEX IDX_FFB38AC4944512CF (_chatroom_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FD32632E8 FOREIGN KEY (_user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F944512CF FOREIGN KEY (_chatroom_id) REFERENCES chatroom (id)');
        $this->addSql('ALTER TABLE user_chatroom ADD CONSTRAINT FK_FFB38AC4D32632E8 FOREIGN KEY (_user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_chatroom ADD CONSTRAINT FK_FFB38AC4944512CF FOREIGN KEY (_chatroom_id) REFERENCES chatroom (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FD32632E8');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F944512CF');
        $this->addSql('ALTER TABLE user_chatroom DROP FOREIGN KEY FK_FFB38AC4D32632E8');
        $this->addSql('ALTER TABLE user_chatroom DROP FOREIGN KEY FK_FFB38AC4944512CF');
        $this->addSql('DROP TABLE chatroom');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_chatroom');
    }
}
