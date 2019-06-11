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

Erasing the customer data is part of the GDPR process, if the customer want its data erased,
you have for example, the choice to anonymize or delete them.  
In the module, that case is represented as a "erase processor", its goal is to apply the erasure
over a component (a scope of an entity type), passed as a key code, and a customer entity id.  
Each strategy is defined as the following interface and must implements it:

`\Opengento\Gdpr\Service\Erase\ProcessorInterface`

The existing ones are: 

- `\Opengento\Gdpr\Service\Erase\Processor\DeleteProcessor`
- `\Opengento\Gdpr\Service\Erase\Processor\AnonymizeProcessor`

These processors are registered in the following factory, if you want to register
your own implementation, add it to the factory via the `di.xml` file configuration:

- `\Opengento\Gdpr\Service\Erase\ProcessorFactory`

```xml
    <type name="Opengento\Gdpr\Service\Erase\ProcessorFactory">
        <arguments>
            <argument name="processors" xsi:type="array">
                <item name="anonymize" xsi:type="string">Opengento\Gdpr\Service\Erase\Processor\AnonymizeProcessor</item>
                <item name="delete" xsi:type="string">Opengento\Gdpr\Service\Erase\Processor\DeleteProcessor</item>
            </argument>
        </arguments>
    </type>
```

The processor responsible of the strategy choice (defined in the config),
is picking the processor from the previous factory, that's why it's not registered in:

- `\Opengento\Gdpr\Service\Erase\Processor\ProcessorStrategy`

As described above, the customer data are splitted to many components, for example,
the order entity type can be represented as a component "sales",
or more granular as simply as "order".  
The default components are:  

- **customer** (customer information data)
- **customer_address** (customer addresses data)
- **quote** (customer quote data and existing pre-filled addresses)
- **order** (customer order data and shipping/billing addresses)
- **subscriber** (customer newsletter information data)

The strategies which are responsible of processing these components.  
That's why you must register, for each strategies, an implementation to process
the related data in the different scope (delete, anonymize, or your own). 

The anonymize and delete processors are described in the following topics:

* [Anonymize Customer Data](/magento2-gdpr/developer-guide/anonymize-customer-data)
* [Delete Customer Data](/magento2-gdpr/developer-guide/delete-customer-data)

**Warning**

Today, if you create a new strategy (as anonymize or delete),
your constructor must have the following argument, which contain the components codes:

`\Magento\Framework\ObjectManager\TMap $processorPool`

However, the implementation of the processor is up to you.
