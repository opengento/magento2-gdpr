# Magento 2 Enhanced Privacy extension for easier compliance with GDPR #

Extension allows customers to delete, anonymize, or export their personal data.

## Getting Started ##

### Prerequisites ###

Magento 2 Open Source or Commerce edition.

### Installation ###

#### Composer ####

From Magento 2 root folder run the commands:  
```
composer require flurrybox/enhanced-privacy
php bin/magento module:enable Flurrybox_EnhancedPrivacy
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
php bin/magento setup:di:compile
```

#### Copy files ####

1. Copy extension files to the `app/code/Flurrybox/EnhancedPrivacy` directory
2. Run the following commands in Magento 2 root folder:
    ```
    php bin/magento module:enable Flurrybox_EnhancedPrivacy
    php bin/magento setup:upgrade
    php bin/magento setup:static-content:deploy
    php bin/magento setup:di:compile
    ```

### Usage and Features ###

* Configuration for this module is located in 'Stores > Configuration > Customers > Customer Configuration > Privacy (GDPR)'.
* Account deletion, anonymization, and export can be done in 'My Account > Privacy Settings'.
* Customers can export their data in .zip archive containing .csv files with personal, wishlist, quote, and address data.
* Customer can delete or anonymize their account. Current password and reason is required. Account will be deleted within 1 hour (or as specified in configuration), in this time span its possible for customers to undo deletion.
* If customer has made at least one order, they are ineligible to delete their account, instead it will be anonymized.
* When a customer visits your store for the first time, a popup notification about cookie policy will be shown.

### Create new export model ###
Besides default export entites its possible to implement custom data export such as - customer data saved in custom database tables by 3rd party integrations.
When customers will make a request for their personal data export, your class instance will be executed by data export processor and will add new file to data archive.

1. Create a new class implementing `Flurrybox\EnhancedPrivacy\Api\DataExportInterface` interface.
    ```php
    <?php
    
    namespace Vendor\Module\Model\Privacy;
    
    use Flurrybox\EnhancedPrivacy\Api\DataExportInterface;
    use Magento\Customer\Api\Data\CustomerInterface;
    
    class EntityExport implements DataExportInterface
    {
        /**
         * Executed upon exporting customer data.
         *
         * Expected return structure:
         *      array(
         *          array('HEADER1', 'HEADER2', 'HEADER3', ...),
         *          array('VALUE1', 'VALUE2', 'VALUE3', ...),
         *          ...
         *      )
         *
         * @param CustomerInterface $customer
         *
         * @return array
         */
        public function export(CustomerInterface $customer)
        {
            ...
        }
    }
    ```
2. Register export class in `etc/di.xml`
    ```xml
    <?xml version="1.0"?>
    <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
        ...
        <type name="Flurrybox\EnhancedPrivacy\Controller\Export\Export">
            <arguments>
                <argument name="processors" xsi:type="array">
                    ...
                    <item name="entity_export" xsi:type="object">Vendor\Module\Model\Privacy\EntityExport</item>
                    ...
                </argument>
            </arguments>
        </type>
        ...
    </config>
    ```

### Create new deletion and anonymization model
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

## Copyrights and License ##

Copyright (c) 2018 Flurrybox, Ltd. under GNU General Public License ("GPL") v3.0
