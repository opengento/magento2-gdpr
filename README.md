# GDPR Module for Magento 2

[![Latest Stable Version](https://img.shields.io/packagist/v/opengento/module-gdpr.svg?style=flat-square)](https://packagist.org/packages/opengento/module-gdpr)
[![License: MIT](https://img.shields.io/github/license/opengento/magento2-gdpr.svg?style=flat-square)](./LICENSE) 

This extension allows customers to delete, anonymize, and export their personal data.

 - [Setup](#setup)
   - [Get the package](#get-the-package)
   - [Install the module](#install-the-module)
 - [Usage](#usage)
 - [Settings](#settings)
 - [Developers](#developers)
   - [Extends Export](#extends-export)
   - [Extends Deletion](#extends-deletion)
   - [Extends Anonymization](#extends-anonymization)
   - [Erasure Strategy](#erasure-strategy)
 - [Support](#support)
 - [Authors](#authors)
 - [License](#license)

## Setup

Magento 2 Open Source or Commerce edition is required.

The Version `3.x` is compliant with Magento `2.3.x`.  
The Version `2.x` is compliant with Magento `2.2.x`.  
The Version `1.x` is compliant with Magento `2.1.x`.

This module does not support Magento `2.0.x`, as this version is not anymore maintained.  

### Get the package

**Zip Package:**

Unzip the package in app/code/Opengento/Gdpr.

**Composer Package:**

```
composer require opengento/module-gdpr
```

### Install the module

Then, run the following magento command:

```
bin/magento setup:upgrade
```

**If you are in production mode, do not forget to recompile and redeploy the static resources.**

## Usage

* **[Art. 17 GDPR](https://gdpr-info.eu/art-17-gdpr/)**
  * Account deletion and anonymization can be done in 'My Account > Privacy Settings'.
  * Customers can use their 'right to be forgotten'. The password is required to ensure the customer legibility.
    The account will be erased within 1 hour, or as specified in configuration. The customer can undo the action in this time span.
* **[Art. 20 GDPR](https://gdpr-info.eu/art-20-gdpr/)**
  * Personal data export can be done in 'My Account > Privacy Settings'.
  * Customers can export their data in `.zip` archive containing file, `.html` (many others are available), with personal data.
* Cookie Policy in a disclosure popup are shown at the first time customer visit.

## Settings

The configuration for this module is located in 'Stores > Configuration > Customers > Customer Configuration > Privacy (GDPR)'.  
The settings are divided as following:

* General Settings
  * Enable the module
  * GDPR Information CMS Page
  * GDPR Information CMS Block
* Erasure Settings
  * Enable the feature
  * Erasure Strategy (Anonymize or Delete)
  * Erasure Time Laps
  * Cron Scheduler
  * Right to Erasure Information CMS Block
  * Anonymization Information CMS Block
  * Remove Customer if no Orders
  * Apply Deletion Strategy to specific components 
* Export Settings
  * Enable the feature
  * Export Personal Data Information CMS Block
  * Export Renderer option
  * Customer Attributes to export
  * Customer Address Attributes to export
* Cookie Settings
  * Enable the cookie disclosure
  * Cookie Policy Information CMS Block

## Developers

The following documentation explains how to add your own processors to the workflow.

### Extends Export

In order to export your custom component, you must create a new processor.  
To create a new processor, you must implement the following interface: `\Opengento\Gdpr\Service\Export\ProcessorInterface`.  
Then, register your processor to the following pool `\Opengento\Gdpr\Service\Export\ProcessorPool`, as described:

```xml
<type name="Opengento\Gdpr\Service\Export\ProcessorPool">
    <arguments>
        <argument name="array" xsi:type="array">
            <item name="my_component" xsi:type="string">\Vendor\Module\ExportProcessor</item>
        </argument>
    </arguments>
</type>
```

You can also create your custom export renderer to make it as be like you want to be.  
To achieve this, you must implement the following interface: `\Opengento\Gdpr\Service\Export\RendererInterface`  
Then, register your renderer to the following pool `\Opengento\Gdpr\Service\Export\RendererPool`, as described:

```xml
<type name="Opengento\Gdpr\Service\Export\RendererPool">
    <arguments>
        <argument name="array" xsi:type="array">
            <item name="my_renderer" xsi:type="string">\Vendor\Module\ExportRenderer</item>
        </argument>
    </arguments>
</type>
```

### Extends Deletion

In order to delete your custom component, you must create a new processor.  
To create a new processor, you must implement the following interface: `\Opengento\Gdpr\Service\Delete\ProcessorInterface`.  
Then, register your processor to the following pool `\Opengento\Gdpr\Service\Delete\ProcessorPool`, as described:

```xml
<type name="Opengento\Gdpr\Service\Delete\ProcessorPool">
    <arguments>
        <argument name="array" xsi:type="array">
            <item name="my_component" xsi:type="string">\Vendor\Module\DeleteProcessor</item>
        </argument>
    </arguments>
</type>
```

### Extends Anonymization

In order to anonymize your custom component, you must create a new processor.  
To create a new processor, you must implement the following interface: `\Opengento\Gdpr\Service\Anonymize\ProcessorInterface`.  
Then, register your processor to the following pool `\Opengento\Gdpr\Service\Anonymize\ProcessorPool`, as described:

```xml
<type name="Opengento\Gdpr\Service\Anonymize\ProcessorPool">
    <arguments>
        <argument name="array" xsi:type="array">
            <item name="my_component" xsi:type="string">\Vendor\Module\AnonymizeProcessor</item>
        </argument>
    </arguments>
</type>
```

### Erasure Strategy

This module allows you to define the strategy to apply for the different processors.  
You can configure it thanks to the admin system configuration, but you can also cheat and
define the strategy to apply for them via the `etc/di.xml` file. Be careful, the settings from the configuration
are always checked in top priority. To make it via the code, add your preferences as following:

```xml
<type name="Opengento\Gdpr\Model\Config\ErasureComponentStrategy">
    <arguments>
        <argument name="componentsStrategies" xsi:type="array">
            <item name="component_name_1" xsi:type="const">Opengento\Gdpr\Service\ErasureStrategy::STRATEGY_ANONYMIZE</item>        
            <item name="component_name_2" xsi:type="const">Opengento\Gdpr\Service\ErasureStrategy::STRATEGY_DELETE</item>        
            <item name="component_name_3" xsi:type="string">custom_strategy_code</item>        
        </argument>
    </arguments>
</type>
```

Warning, if you want to implement your own strategy type, you must create your own strategy class object, but you will be able to use the `Opengento\Gdpr\Model\Config\ErasureComponentStrategy` to serve your components by strategy.  
Do not forget to use the right services managers, but you are free to use yours:  
- `Opengento\Gdpr\Service\AnonymizeManagement`
- `Opengento\Gdpr\Service\DeleteManagement`

## Support

Raise a new [request](https://github.com/opengento/magento2-gdpr/issues) to the issue tracker.  
Please provide your Magento 2 version and the module version. Explain how to reproduce your issue and what's expected.

## Authors

- **Initial Inspiration** - *`Cookie PopUp` sources* - [flurrybox](https://github.com/flurrybox)
- **Opengento Community** - *Lead* - [They're awesome!](https://github.com/opengento)
- **Contributors** - *Contributor* - [Many thanks!](https://github.com/opengento/magento2-gdpr/graphs/contributors)

## Similar Magento 2 GDPR Module

- https://github.com/AdFabConnect/magento2gdpr
- https://github.com/mageplaza/magento-2-gdpr
- https://github.com/staempfli/magento2-module-gdpr
- https://github.com/flurrybox/enhanced-privacy

## License

This project is licensed under the MIT License - see the [LICENSE](./LICENSE) details.

***That's all folks!***
