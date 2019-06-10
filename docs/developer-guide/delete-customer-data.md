# Developer Guide

[![Latest Stable Version](https://img.shields.io/packagist/v/opengento/module-gdpr.svg?style=flat-square)](https://packagist.org/packages/opengento/module-gdpr)
[![License: MIT](https://img.shields.io/github/license/opengento/magento2-gdpr.svg?style=flat-square)](./LICENSE)

[![Packagist](https://img.shields.io/packagist/dt/opengento/module-gdpr.svg?style=flat-square)](https://packagist.org/packages/opengento/module-gdpr)
[![Packagist](https://img.shields.io/packagist/dm/opengento/module-gdpr.svg?style=flat-square)](https://packagist.org/packages/opengento/module-gdpr)

[![GitHub forks](https://img.shields.io/github/forks/opengento/magento2-gdpr.svg?style=social)](https://github.com/opengento/magento2-gdpr/network/members)
[![GitHub stars](https://img.shields.io/github/stars/opengento/magento2-gdpr.svg?style=social)](https://github.com/opengento/magento2-gdpr/stargazers)
[![GitHub watchers](https://img.shields.io/github/watchers/opengento/magento2-gdpr.svg?style=social)](https://github.com/opengento/magento2-gdpr/watchers)

___

[BACK TO THE MENU](/magento2-gdpr/)

___

The following documentation explains how to add your own processors to the workflow.

* [Developer Guide](/magento2-gdpr/developer-guide/)
* [Erase Customer Data](/magento2-gdpr/developer-guide/erase-customer-data)
    * [Anonymize Customer Data](/magento2-gdpr/developer-guide/anonymize-customer-data)
    * Delete Customer Data
* [Export Customer Data](/magento2-gdpr/developer-guide/export-customer-data)

## Delete Customer Data

In order to delete your custom component, you must create a new processor.  
To create a new processor, you must implement the following interface: `\Opengento\Gdpr\Service\Delete\ProcessorInterface`.  
Then, register your processor to the following pool `\Opengento\Gdpr\Service\Delete\ProcessorPool`, as described:

```xml
<type name="Opengento\Gdpr\Service\Delete\ProcessorPool">
    <arguments>
        <argument name="array" xsi:type="array">
            <item name="my_component" xsi:type="string">Vendor\Module\DeleteProcessor</item>
        </argument>
    </arguments>
</type>
```
