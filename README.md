# Gdpr Module for Magento 2

[![Latest Stable Version](https://img.shields.io/packagist/v/opengento/module-gdpr.svg?style=flat-square)](https://packagist.org/packages/opengento/module-gdpr)
[![License: MIT](https://img.shields.io/github/license/opengento/magento2-rgpd.svg?style=flat-square)](./LICENSE) 


Extension allows customers to delete, anonymize, or export their personal data.


## Setup

Magento 2 Open Source or Commerce edition are required.

### Get the package

**Zip Package:**

Unzip the package in app/code/Opengento/Gdpr.

**Composer Package:**

```
composer require opengento/magento2-gdpr
```

### Install the module

Then, run the following magento command:

```
php bin/magento setup:upgrade
```

**If you are in production mode, do not forget to recompile and redeploy the static resources.**

## Usage

* Configuration for this module is located in 'Stores > Configuration > Customers > Customer Configuration > Privacy (GDPR)'.
* Account deletion, anonymization, and export can be done in 'My Account > Privacy Settings'.
* Customers can export their data in .zip archive containing .csv files with personal, wishlist, quote, and address data.
* Customer can delete or anonymize their account. Current password and reason is required. Account will be deleted within 1 hour (or as specified in configuration), in this time span its possible for customers to undo deletion.
* If customer has made at least one order, they are ineligible to delete their account, instead it will be anonymized.
* When a customer visits your store for the first time, a popup notification about cookie policy will be shown.


## Settings

To configure the module, go to the admin panel, then go to Stores > Configuration > Customer

## Developers

The following documentation explain how to add your own processors to the workflow.

### Extends Export

If you need to export third-part related customer data:

1. Implements the `Flurrybox\EnhancedPrivacy\Service\Export\ProcessorInterface` interface.
    ```php
    <?php
    
    namespace Vendor\Module\Model\Privacy;
    
    use Flurrybox\EnhancedPrivacy\Service\Export\ProcessorInterface;
    
    class EntityExport implements ProcessorInterface
    {   
        public function execute(string $email, array $data): array 
        {
            ...
            return $data;
        }
    }
    ```

2. Register export class in `etc/di.xml`
    ```xml
        <type name="Flurrybox\EnhancedPrivacy\Controller\Export\Export">
            <arguments>
                <argument name="processors" xsi:type="array">
                    ...
                    <item name="entity_export" xsi:type="object">Vendor\Module\Model\Privacy\EntityExport</item>
                    ...
                </argument>
            </arguments>
        </type>
    ```

### Extends Deletion
To delete data thats gathered by 3rd party integrations you can implement your own data processor.

1. Create a new class implementing `Flurrybox\EnhancedPrivacy\Api\DataDeleteInterface` interface.
    ```php
    <?php
    
    namespace Vendor\Module\Model\Privacy;
    
    use Flurrybox\EnhancedPrivacy\Api\DataDeleteInterface;
    use Magento\Customer\Api\Data\CustomerInterface;
    
    class EntityDelete implements DataDeleteInterface
    {
        /**
         * Executed upon customer data deletion.
         *
         * @param CustomerInterface $customer
         *
         * @return void
         */
        public function delete(CustomerInterface $customer)
        {
            ...
        }
        
        /**
         * Executed upon customer data anonymization.
         *
         * @param CustomerInterface $customer
         *
         * @return void
         */
        public function anonymize(CustomerInterface $customer)
        {
            ...
        }
    }
    ```
2. Register processor class in `etc/di.xml`
    ```xml
    <?xml version="1.0"?>
    <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
        ...
        <type name="Flurrybox\EnhancedPrivacy\Cron\Schedule">
            <arguments>
                <argument name="processors" xsi:type="array">
                    ...
                    <item name="entity_delete" xsi:type="object">Vendor\Module\Model\Privacy\EntityDelete</item>
                    ...
                </argument>
            </arguments>
        </type>
        ...
    </config>
    ```

### Extends Anonymization



## Support

- Raise a new [request](https://github.com/opengento/magento2-rgpd/issues) on the issue tracker.

## Authors

- **Opengento Community** - *Lead* - [They're awesome!](https://github.com/opengento)
- **Contributors** - *Contributor* - [Many thanks!](https://github.com/opengento/magento2-rgpd/graphs/contributors)

## Licence

This project is licensed under the MIT License - see the [LICENSE](./LICENSE) details.

***That's all folks!***
