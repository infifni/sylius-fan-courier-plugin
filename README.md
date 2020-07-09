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
    2.2. Add plugin dependencies to your `config/bundles.php` file (this is done automatically with flex):
    ```php
    // config/bundles.php
    return [
        // other lines
        Infifni\SyliusFanCourierPlugin\InfifniSyliusFanCourierPlugin::class => ['all' => true],
    ];
    ```
    2.3. Import resource:
    ```yaml
    // config/packages/infifni_sylius_fan_courier_plugin.yaml
    imports:
       - { resource: "@InfifniSyliusFanCourierPlugin/Resources/config/resource/infifni_shipping_awb.yml" }
    ```
    2.4. Execute database migration (check the queries in case you already have data in database):
    ```bash
    cd /project/root
    cp vendor/infifni/sylius-fan-courier-plugin/src/Migrations/Version20200606093404.php src/Migrations
    bin/console doctrine:mig:mig
    ```
3. Define a shipping method at /admin/shipping-methods/new.
    - name it FAN Courier and whatever pleases you
    - the only mandatory thing here is to select for Calculator the FAN calculator, it will be used for cost estimation
4. Define a new gateway at /admin/shipping-gateways/new/fan and use the shipping method defined at 4.
   
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