<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250907105143 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE barber_wallet (id INT AUTO_INCREMENT NOT NULL, barber_id INT NOT NULL, balance NUMERIC(10, 2) NOT NULL, total_earned NUMERIC(10, 2) NOT NULL, date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', UNIQUE INDEX UNIQ_41C6CE6BBFF2FEF2 (barber_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE barber_wallet ADD CONSTRAINT FK_41C6CE6BBFF2FEF2 FOREIGN KEY (barber_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE payment ADD wallet_id INT DEFAULT NULL, CHANGE invoice_id invoice_id INT NOT NULL');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D712520F3 FOREIGN KEY (wallet_id) REFERENCES barber_wallet (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_6D28840D712520F3 ON payment (wallet_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D712520F3');
        $this->addSql('ALTER TABLE barber_wallet DROP FOREIGN KEY FK_41C6CE6BBFF2FEF2');
        $this->addSql('DROP TABLE barber_wallet');
        $this->addSql('DROP INDEX IDX_6D28840D712520F3 ON payment');
        $this->addSql('ALTER TABLE payment DROP wallet_id, CHANGE invoice_id invoice_id INT DEFAULT NULL');
    }
}
