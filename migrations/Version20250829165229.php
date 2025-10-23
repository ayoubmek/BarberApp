<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250829165229 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_64C19C15E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE history (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, client_name VARCHAR(100) NOT NULL, client_phone VARCHAR(20) DEFAULT NULL, date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', total_amount NUMERIC(10, 2) NOT NULL, payment_method VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice_item (id INT AUTO_INCREMENT NOT NULL, invoice_id INT NOT NULL, service_id INT NOT NULL, barber_id INT NOT NULL, quantity INT NOT NULL, price NUMERIC(10, 2) NOT NULL, INDEX IDX_1DDE477B2989F1FD (invoice_id), INDEX IDX_1DDE477BED5CA9E6 (service_id), INDEX IDX_1DDE477BBFF2FEF2 (barber_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, invoice_id INT NOT NULL, amount NUMERIC(10, 2) NOT NULL, payment_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', payment_type VARCHAR(10) NOT NULL, INDEX IDX_6D28840D2989F1FD (invoice_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, price NUMERIC(10, 2) NOT NULL, duration INT NOT NULL, INDEX IDX_E19D9AD212469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, email VARCHAR(150) NOT NULL, password VARCHAR(255) NOT NULL, phone VARCHAR(20) DEFAULT NULL, role VARCHAR(10) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invoice_item ADD CONSTRAINT FK_1DDE477B2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE invoice_item ADD CONSTRAINT FK_1DDE477BED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE invoice_item ADD CONSTRAINT FK_1DDE477BBFF2FEF2 FOREIGN KEY (barber_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD212469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice_item DROP FOREIGN KEY FK_1DDE477B2989F1FD');
        $this->addSql('ALTER TABLE invoice_item DROP FOREIGN KEY FK_1DDE477BED5CA9E6');
        $this->addSql('ALTER TABLE invoice_item DROP FOREIGN KEY FK_1DDE477BBFF2FEF2');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D2989F1FD');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD212469DE2');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE history');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE invoice_item');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
