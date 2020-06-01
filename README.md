<a href="https://infifnisoftware.ro" target="_blank">
    <img src="https://infifnisoftware.ro/themes/custom/infifni/logo.svg" alt="infifni logo" height="200" />
</a>
<h1>
    Sylius FAN Courier plugin
    <br />
    License MIT
</h1>

<p>
This plugin works with selfawb.ro API and provides shipping cost estimation
from FAN Courier provider (using their API).

This plugin was made on top of Bitbag Shipping Export abstraction layer, with some modifications.

Also the services provided by FAN Courier are based in Romania, you'll most likely have errors for other regions. 
</p>

## Prerequisites
You'll need an account on https://www.selfawb.ro/ and also a contract that will be signed
after you make an explicit request to sales@fancourier.ro.
There are many services to choose from, the one you choose must be also selected when defining a
shipping gateway at point 4.

## Installation
1. Install Bitbag Shipping Export plugin, the Infifni modified version.

    1.1. Add a section repositories to your main application composer.json file with the following:
    ```json
    {
        "repositories": [
            {
                "type": "vcs",
                "url": "https://github.com/infifni/SyliusShippingExportPlugin"
            }
        ]
    }
    ```

    1.2. Follow installation steps from [https://github.com/infifni/SyliusShippingExportPlugin](https://github.com/infifni/SyliusShippingExportPlugin)

2. Install Sylius FAN Courier plugin.
    
    2.1. Composer install.
    ```bash
    composer require infifni/sylius-fan-courier-plugin
    ```
   2.2. Add plugin dependencies to your `config/bundles.php` file:
    ```php
    // config/bundles.php
    return [
        // other lines
        new Infifni\SyliusFanCourierPlugin\InfifniSyliusFanCourierPlugin(),
    ];
    ```
3. Define a shipping method at /admin/shipping-methods/new.
    - name it FAN Courier and whatever pleases you
    - the only mandatory thing here is to select for Calculator the FAN calculator, it will be used for cost estimation
4. Define a new gateway at /admin/shipping-gateways/new/fan and use the shipping method defined at 4.
    
5. It is mandatory to have provinces defined and must correspond to those of FAN Courier's internal references, here 
are the queries you need to execute to have them:
    ```sql
    -- might not work if you already loaded sample data
   
    INSERT INTO `sylius_country` (`id`, `code`, `enabled`) VALUES(1, 'RO', 1);
    
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'AB', 'Alba', 'RO_AB');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'AG', 'Argeș', 'RO_AG');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'AR', 'Arad', 'RO_AR');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'B', 'București', 'RO_B');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'BC', 'Bacău', 'RO_BC');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'BH', 'Bihor', 'RO_BH');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'BN', 'Bistrița-Năsăud', 'RO_BN');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'BR', 'Brăila', 'RO_BR');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'BT', 'Botoșani', 'RO_BT');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'BV', 'Brașov', 'RO_BV');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'BZ', 'Buzău', 'RO_BZ');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'CJ', 'Cluj', 'RO_CJ');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'CL', 'Călărași', 'RO_CL');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'CS', 'Caraș-Severin', 'RO_CS');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'CT', 'Constanța', 'RO_CT');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'CV', 'Covasna', 'RO_CV');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'DB', 'Dâmbovița', 'RO_DB');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'DJ', 'Dolj', 'RO_DJ');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'GJ', 'Gorj', 'RO_GJ');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'GL', 'Galați', 'RO_GL');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'GR', 'Giurgiu', 'RO_GR');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'HD', 'Hunedoara', 'RO_HD');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'HR', 'Harghita', 'RO_HR');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'IF', 'Ilfov', 'RO_IF');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'IL', 'Ialomița', 'RO_IL');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'IS', 'Iași', 'RO_IS');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'MH', 'Mehedinți', 'RO_MH');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'MM', 'Maramureș', 'RO_MM');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'MS', 'Mureș', 'RO_MS');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'NT', 'Neamț', 'RO_NT');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'OT', 'Olt', 'RO_OT');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'PH', 'Prahova', 'RO_PH');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'SB', 'Sibiu', 'RO_SB');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'SJ', 'Sălaj', 'RO_SJ');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'SM', 'Satu Mare', 'RO_SM');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'SV', 'Suceava', 'RO_SV');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'TL', 'Tulcea', 'RO_TL');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'TM', 'Timiș', 'RO_TM');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'TR', 'Teleorman', 'RO_TR');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'VL', 'Vâlcea', 'RO_VL');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'VN', 'Vrancea', 'RO_VN');
    INSERT INTO `sylius_province` (`country_id`, `code`, `name`, `abbreviation`) VALUES (1, 'VS', 'Vaslui', 'RO_VS');
    
    INSERT INTO `sylius_zone` (`id`, `code`, `name`, `type`, `scope`) VALUES (1, 'RO', 'ROMANIA', 'country', 'all');
    
    INSERT INTO `sylius_zone_member` (`id`, `belongs_to`, `code`) VALUES (1, 1, 'RO');
    ```
   
## Common pitfalls

| Pitfall                  | Why                                   | Exception thrown                     | What user sees                       |
|--------------------------|---------------------------------------|--------------------------------------|--------------------------------------|
| Incorrect province name  | - provinces are not inserted as specified above or plugin is used for geographical areas outside of Romania | \Infifni\SyliusFanCourierPlugin\Exception\WrongProvinceNameException | Costul de transport afișat este greșit din cauza unei erori de sistem. Vă rugăm continuați comanda, se va regla manual de către un operator ulterior ! |
| Incorrect city name      | - city does not exist in FAN's database; diacritics are removed by the plugin if any; all uppercase letters are converted to lowercase and spaces are trimmed before doing the FAN request | \Infifni\SyliusFanCourierPlugin\Exception\WrongCityNameException | Orașul introdus nu este recunoscut în sistem, costul de transport afișat nu este real. Situația se va regla manual de către un operator ulterior ! |

What to do ?

If you've experienced such a pitfall normally you can solve it by changing the names provided.
If the error persists please report an issue in Github.

You can also regularly watch the logs (dev.log, prod.log) and search errors containing 'Shipping estimation failed for shipment with id'.
If you can solve them please do and do a pull request to the project. If not please report the issue, especially if it's not an 
exception enumerated above.