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

Deleting the customer data is one the erasure strategy. Whole data given to this type of processor is deleted.  
It implements the following interface:

- `\Opengento\Gdpr\Service\Delete\ProcessorInterface`  

The processors are registered to the following pool, if you want to register you own implementation,
add it to the pool via the `di.xml` file configuration:

- `\Opengento\Gdpr\Model\Customer\Delete\ProcessorPool` for Logged In Customer
- `\Opengento\Gdpr\Model\Order\Delete\ProcessorPool` for Guest Customer

```xml
<!-- Delete Customer Management -->
<virtualType name="Opengento\Gdpr\Model\Customer\Delete\ProcessorPool" type="Magento\Framework\ObjectManager\TMap">
    <arguments>
        <argument name="type" xsi:type="string">Opengento\Gdpr\Service\Erase\ProcessorInterface</argument>
        <argument name="array" xsi:type="array">
            <item name="subscriber" xsi:type="string">Opengento\Gdpr\Model\Customer\Delete\Processor\SubscriberDataProcessor</item>
            <item name="quote" xsi:type="string">Opengento\Gdpr\Model\Customer\Delete\Processor\QuoteDataProcessor</item>
            <item name="order" xsi:type="string">Opengento\Gdpr\Model\Customer\Delete\Processor\OrderDataProcessor</item>
            <item name="customer_address" xsi:type="string">Opengento\Gdpr\Model\Customer\Delete\Processor\CustomerAddressDataProcessor</item>
            <item name="customer" xsi:type="string">Opengento\Gdpr\Model\Customer\Delete\Processor\CustomerDataProcessor</item>
        </argument>
    </arguments>
</virtualType>
<!-- Delete Guest Management -->
<virtualType name="Opengento\Gdpr\Model\Order\Delete\ProcessorPool" type="Magento\Framework\ObjectManager\TMap">
    <arguments>
        <argument name="type" xsi:type="string">Opengento\Gdpr\Service\Erase\ProcessorInterface</argument>
        <argument name="array" xsi:type="array">
            <item name="subscriber" xsi:type="string">Opengento\Gdpr\Model\Order\Delete\Processor\SubscriberDataProcessor</item>
            <item name="quote" xsi:type="string">Opengento\Gdpr\Model\Order\Delete\Processor\QuoteDataProcessor</item>
            <item name="order" xsi:type="string">Opengento\Gdpr\Model\Order\Delete\Processor\OrderDataProcessor</item>
        </argument>
    </arguments>
</virtualType>
```
