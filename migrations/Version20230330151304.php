<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230330151304 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity_activity_tag (activity_id INT NOT NULL, activity_tag_id INT NOT NULL, INDEX IDX_7808EB4081C06096 (activity_id), INDEX IDX_7808EB40E8A48311 (activity_tag_id), PRIMARY KEY(activity_id, activity_tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE activity_tag (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE period_period_tag (period_id INT NOT NULL, period_tag_id INT NOT NULL, INDEX IDX_61D3C63AEC8B7ADE (period_id), INDEX IDX_61D3C63A873B5E4E (period_tag_id), PRIMARY KEY(period_id, period_tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE period_tag (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activity_activity_tag ADD CONSTRAINT FK_7808EB4081C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activity_activity_tag ADD CONSTRAINT FK_7808EB40E8A48311 FOREIGN KEY (activity_tag_id) REFERENCES activity_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE period_period_tag ADD CONSTRAINT FK_61D3C63AEC8B7ADE FOREIGN KEY (period_id) REFERENCES period (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE period_period_tag ADD CONSTRAINT FK_61D3C63A873B5E4E FOREIGN KEY (period_tag_id) REFERENCES period_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE period ADD title VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity_activity_tag DROP FOREIGN KEY FK_7808EB4081C06096');
        $this->addSql('ALTER TABLE activity_activity_tag DROP FOREIGN KEY FK_7808EB40E8A48311');
        $this->addSql('ALTER TABLE period_period_tag DROP FOREIGN KEY FK_61D3C63AEC8B7ADE');
        $this->addSql('ALTER TABLE period_period_tag DROP FOREIGN KEY FK_61D3C63A873B5E4E');
        $this->addSql('DROP TABLE activity_activity_tag');
        $this->addSql('DROP TABLE activity_tag');
        $this->addSql('DROP TABLE period_period_tag');
        $this->addSql('DROP TABLE period_tag');
        $this->addSql('ALTER TABLE period DROP title');
    }
}
