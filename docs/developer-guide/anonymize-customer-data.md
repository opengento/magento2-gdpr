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

In order to anonymize your custom component, you must create a new processor.  
To create a new processor, you must implement the following interface: `\Opengento\Gdpr\Service\Anonymize\ProcessorInterface`.  
Then, register your processor to the following pool `\Opengento\Gdpr\Service\Anonymize\ProcessorPool`, as described:

```xml
<type name="Opengento\Gdpr\Service\Anonymize\ProcessorPool">
    <arguments>
        <argument name="array" xsi:type="array">
            <item name="my_component" xsi:type="string">Vendor\Module\AnonymizeProcessor</item>
        </argument>
    </arguments>
</type>
```

### Anonymize Specific Data

See the following implementation of `\Opengento\Gdpr\Model\Entity\EntityValueProcessorInterface`:

`\Opengento\Gdpr\Service\Anonymize\Processor\Entity\EntityValue\Processor`

Instantiate this class with a list of attributes codes and the anonymize to apply with, and a document to storage the results:

- `\Opengento\Gdpr\Model\Entity\DocumentInterface`
- `\Opengento\Gdpr\Model\Entity\MetadataInterface`
- `\Opengento\Gdpr\Service\Anonymize\AnonymizerInterface`

## Important

The newsletter integration in Magento does not follows the Service Contract Pattern applied to the Magento 2 core.  
Actually the `Subscriber` model only exists as its own `AbstarctModel` and has no preference over an API interface.  
As the existing model is not final, it can be plugged and an interceptor is generation on the compilation.  
It has for side effect to break the data collector resolve by type hitting.  
That's why the subscriber model is extender in our own class and marked as final.

`\Opengento\Gdpr\Model\Newsletter\Subscriber` class is the final state of `\Magento\Newsletter\Model\Subscriber`.
