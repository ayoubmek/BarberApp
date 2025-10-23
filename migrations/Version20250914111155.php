<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250914111155 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE barber_wallet DROP INDEX UNIQ_41C6CE6BBFF2FEF2, ADD INDEX IDX_41C6CE6BBFF2FEF2 (barber_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BARBER_DATE ON barber_wallet (barber_id, date)');
        $this->addSql('ALTER TABLE payment CHANGE invoice_id invoice_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE barber_wallet DROP INDEX IDX_41C6CE6BBFF2FEF2, ADD UNIQUE INDEX UNIQ_41C6CE6BBFF2FEF2 (barber_id)');
        $this->addSql('DROP INDEX UNIQ_BARBER_DATE ON barber_wallet');
        $this->addSql('ALTER TABLE payment CHANGE invoice_id invoice_id INT DEFAULT NULL');
    }
}
