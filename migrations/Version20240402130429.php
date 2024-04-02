<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240402130429 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product (id UUID NOT NULL, name VARCHAR(255) NOT NULL, price VARCHAR(32) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN product.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN product.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE shopping_cart (id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN shopping_cart.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN shopping_cart.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE shopping_cart_product (id UUID NOT NULL, shopping_cart_id UUID DEFAULT NULL, product_id UUID DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, amount INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FA1F5E6C45F80CD ON shopping_cart_product (shopping_cart_id)');
        $this->addSql('CREATE INDEX IDX_FA1F5E6C4584665A ON shopping_cart_product (product_id)');
        $this->addSql('COMMENT ON COLUMN shopping_cart_product.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN shopping_cart_product.shopping_cart_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN shopping_cart_product.product_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN shopping_cart_product.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE shopping_cart_product ADD CONSTRAINT FK_FA1F5E6C45F80CD FOREIGN KEY (shopping_cart_id) REFERENCES shopping_cart (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE shopping_cart_product ADD CONSTRAINT FK_FA1F5E6C4584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE shopping_cart_product DROP CONSTRAINT FK_FA1F5E6C45F80CD');
        $this->addSql('ALTER TABLE shopping_cart_product DROP CONSTRAINT FK_FA1F5E6C4584665A');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE shopping_cart');
        $this->addSql('DROP TABLE shopping_cart_product');
    }
}
