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
* Erase Customer Data
    * [Anonymize Customer Data](/magento2-gdpr/developer-guide/anonymize-customer-data)
    * [Delete Customer Data](/magento2-gdpr/developer-guide/delete-customer-data)
* [Export Customer Data](/magento2-gdpr/developer-guide/export-customer-data)

## Erase Customer Data

This module allows you to define the strategy to apply for the different processors.  
You can configure it thanks to the admin system configuration, but you can also cheat and
define the strategy to apply for them via the `etc/di.xml` file. Be careful, the settings from the configuration
are always checked in top priority. To make it via the code, add your preferences as following:

```xml
<type name="Opengento\Gdpr\Service\Erase\EraseFactory">
    <arguments>
        <argument name="processors" xsi:type="array">
            <item name="anonymize" xsi:type="string">Opengento\Gdpr\Service\Erase\Processor\AnonymizeProcessor</item>
            <item name="delete" xsi:type="string">Opengento\Gdpr\Service\Erase\Processor\DeleteProcessor</item>        
            <item name="your_custom_strategy" xsi:type="string">Vendor\Module\EraseProcessor</item>        
        </argument>
    </arguments>
</type>
```
