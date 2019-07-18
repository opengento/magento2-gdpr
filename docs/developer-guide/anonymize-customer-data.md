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
    * Anonymize Customer Data
    * [Delete Customer Data](/magento2-gdpr/developer-guide/delete-customer-data)
* [Export Customer Data](/magento2-gdpr/developer-guide/export-customer-data)

## Anonymize Customer Data

Anonymize the customer data is one of the erasure strategy, rather than removing the data it actually process to set the
information not identifiable by any other parties.   
It implements the following interface:

- `\Opengento\Gdpr\Service\Anonymize\ProcessorInterface`

The processors are registered to the following pool, if you want to register you own implementation,
add it to the pool via the `di.xml` file configuration:

- `\Opengento\Gdpr\Service\Anonymize\ProcessorPool`

```xml
<virtualType name="Opengento\Gdpr\Service\Anonymize\ProcessorPool" type="Magento\Framework\ObjectManager\TMap">
    <arguments>
        <argument name="type" xsi:type="string">Opengento\Gdpr\Service\Anonymize\ProcessorInterface</argument>
        <argument name="array" xsi:type="array">
            <item name="customer" xsi:type="string">Opengento\Gdpr\Service\Anonymize\Processor\CustomerDataProcessor</item>
            <item name="customer_address" xsi:type="string">Opengento\Gdpr\Service\Anonymize\Processor\CustomerAddressDataProcessor</item>
            <item name="quote" xsi:type="string">Opengento\Gdpr\Service\Anonymize\Processor\QuoteDataProcessor</item>
            <item name="order" xsi:type="string">Opengento\Gdpr\Service\Anonymize\Processor\OrderDataProcessor</item>
            <item name="subscriber" xsi:type="string">Opengento\Gdpr\Service\Anonymize\Processor\SubscriberDataProcessor</item>
        </argument>
    </arguments>
</virtualType>
```

### Anonymize Service Package

This parts describe the interfaces, classes and methods that can be used over your implementation.

#### Anonymizer

The anonymizer role is to obfuscate a value. It implements the following interface:

- `\Opengento\Gdpr\Service\Anonymize\AnonymizerInterface`

The anonymizers are registered in the following factory, if you want to register
your own implementation, add it to the factory via the `di.xml` file configuration:

- `\Opengento\Gdpr\Service\Anonymize\AnonymizerFactory`

```xml
<type name="Opengento\Gdpr\Service\Anonymize\AnonymizerFactory">
    <arguments>
        <argument name="anonymizers" xsi:type="array">
            <item name="default" xsi:type="string">Opengento\Gdpr\Service\Anonymize\Anonymizer\Anonymous</item>
            <item name="anonymous" xsi:type="string">Opengento\Gdpr\Service\Anonymize\Anonymizer\Anonymous</item>
            <item name="date" xsi:type="string">Opengento\Gdpr\Service\Anonymize\Anonymizer\Date</item>
            <item name="email" xsi:type="string">Opengento\Gdpr\Service\Anonymize\Anonymizer\Email</item>
            <item name="phone" xsi:type="string">Opengento\Gdpr\Service\Anonymize\Anonymizer\Phone</item>
            <item name="number" xsi:type="string">Opengento\Gdpr\Service\Anonymize\Anonymizer\Number</item>
            <item name="alphaLower" xsi:type="string">Opengento\Gdpr\Service\Anonymize\Anonymizer\AlphaLower</item>
            <item name="alphaUpper" xsi:type="string">Opengento\Gdpr\Service\Anonymize\Anonymizer\AlphaUpper</item>
            <item name="alphaNum" xsi:type="string">Opengento\Gdpr\Service\Anonymize\Anonymizer\AlphaNum</item>
            <item name="nullValue" xsi:type="string">Opengento\Gdpr\Service\Anonymize\Anonymizer\NullValue</item>
            <item name="street" xsi:type="string">Opengento\Gdpr\Service\Anonymize\Anonymizer\Street</item>
        </argument>
    </arguments>
</type>
```

When the anonymizers are registered in the factory, they are available to use on the administrator configuration view.
The anonymizer can be used for one or many selected attributes.

#### Anonymize Specific Data

The existing components processors uses an advanced tool which allows to loop through an entity attributes and apply a
specific call over them. This tool is described in the following package:

- `\Opengento\Gdpr\Model\Entity`

This package is used in the anonymizer processor of each components, it allows to reuse generic stuff and so on is
easier to maintain/configure.

See the following implementation of `\Opengento\Gdpr\Model\Entity\EntityValueProcessorInterface`:

- `\Opengento\Gdpr\Service\Anonymize\Processor\Entity\EntityValue\Processor`

Instantiate this class with a list of attributes codes and the anonymize to apply with,
and a document to storage the results:

- `\Opengento\Gdpr\Model\Entity\DocumentInterface`
- `\Opengento\Gdpr\Model\Entity\MetadataInterface`
- `\Opengento\Gdpr\Service\Anonymize\AnonymizerInterface`

## Important

The newsletter integration in Magento does not follows the Service Contract Pattern applied to the Magento 2 core.  
Actually the `Subscriber` model only exists as its own `AbstractModel` and has no preference over an API interface.  
As the existing model is not final, it can be plugged and an interceptor is create on the compilation.  
It has side effect and break the data collector resolver by type hitting.  
That's why the subscriber model is used in composition in our own class and marked as final. All call are delegated to
the original subscriber model (with their plugin and preferences).

`\Opengento\Gdpr\Model\Newsletter\Subscriber` class is the final state of `\Magento\Newsletter\Model\Subscriber`.
