<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200606093404 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT INTO `sylius_country` (`id`, `code`, `enabled`) VALUES(1, 'RO', 1);");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'AB', 'Alba', 'RO-AB');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'AG', 'Argeș', 'RO-AG');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'AR', 'Arad', 'RO-AR');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'B', 'București', 'RO-B');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'BC', 'Bacău', 'RO-BC');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'BH', 'Bihor', 'RO-BH');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'BN', 'Bistrița-Năsăud', 'RO-BN');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'BR', 'Brăila', 'RO-BR');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'BT', 'Botoșani', 'RO-BT');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'BV', 'Brașov', 'RO-BV');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'BZ', 'Buzău', 'RO-BZ');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'CJ', 'Cluj', 'RO-CJ');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'CL', 'Călărași', 'RO-CL');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'CS', 'Caraș-Severin', 'RO-CS');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'CT', 'Constanța', 'RO-CT');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'CV', 'Covasna', 'RO-CV');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'DB', 'Dâmbovița', 'RO-DB');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'DJ', 'Dolj', 'RO-DJ');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'GJ', 'Gorj', 'RO-GJ');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'GL', 'Galați', 'RO-GL');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'GR', 'Giurgiu', 'RO-GR');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'HD', 'Hunedoara', 'RO-HD');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'HR', 'Harghita', 'RO-HR');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'IF', 'Ilfov', 'RO-IF');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'IL', 'Ialomița', 'RO-IL');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'IS', 'Iași', 'RO-IS');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'MH', 'Mehedinți', 'RO-MH');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'MM', 'Maramureș', 'RO-MM');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'MS', 'Mureș', 'RO-MS');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'NT', 'Neamț', 'RO-NT');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'OT', 'Olt', 'RO-OT');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'PH', 'Prahova', 'RO-PH');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'SB', 'Sibiu', 'RO-SB');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'SJ', 'Sălaj', 'RO-SJ');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'SM', 'Satu Mare', 'RO-SM');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'SV', 'Suceava', 'RO-SV');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'TL', 'Tulcea', 'RO-TL');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'TM', 'Timiș', 'RO-TM');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'TR', 'Teleorman', 'RO-TR');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'VL', 'Vâlcea', 'RO-VL');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'VN', 'Vrancea', 'RO-VN');");
        $this->addSql("INSERT INTO `sylius_province` (`country_id`, `abbreviation`, `name`, `code`) VALUES (1, 'VS', 'Vaslui', 'RO-VS');");
        $this->addSql("INSERT INTO `sylius_zone` (`id`, `code`, `name`, `type`, `scope`) VALUES (1, 'RO', 'ROMANIA', 'country', 'all');");
        $this->addSql("INSERT INTO `sylius_zone_member` (`id`, `belongs_to`, `code`) VALUES (1, 1, 'RO');");

        $this->addSql('CREATE TABLE infifni_fan_shipping_awb (id INT AUTO_INCREMENT NOT NULL, shipment_id INT DEFAULT NULL, awb VARCHAR(20) NOT NULL, api_response LONGTEXT NOT NULL, cost DOUBLE PRECISION DEFAULT NULL, country_code VARCHAR(255) NOT NULL, province_code VARCHAR(255) DEFAULT NULL, city VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_C205C2A075969740 (awb), UNIQUE INDEX UNIQ_C205C2A07BE036FC (shipment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE infifni_fan_shipping_awb ADD CONSTRAINT FK_C205C2A07BE036FC FOREIGN KEY (shipment_id) REFERENCES sylius_shipment (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM `sylius_zone_member` WHERE `id` = 1');
        $this->addSql('DELETE FROM `sylius_zone` WHERE `id` = 1');
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-AB'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-AG'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-AR'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-B'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-BC'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-BH'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-BN'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-BR'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-BT'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-BV'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-BZ'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-CJ'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-CL'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-CS'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-CT'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-CV'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-DB'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-DJ'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-GJ'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-GL'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-GR'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-HD'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-HR'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-IF'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-IL'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-IS'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-MH'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-MM'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-MS'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-NT'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-OT'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-PH'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-SB'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-SJ'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-SM'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-SV'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-TL'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-TM'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-TR'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-VL'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-VN'");
        $this->addSql("DELETE FROM `sylius_province` WHERE `code` = 'RO-VS'");
        $this->addSql("DELETE FROM `sylius_country` WHERE `id` = 1");

        $this->addSql("ALTER TABLE `infifni_fan_shipping_awb` DROP FOREIGN KEY `FK_C205C2A07BE036FC`");
        $this->addSql("DROP TABLE `infifni_fan_shipping_awb`");
    }
}
