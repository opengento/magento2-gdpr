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
    * [Delete Customer Data](/magento2-gdpr/developer-guide/delete-customer-data)
* Export Customer Data

## Export Customer Data

The export processor job is to loop through the customer data and export the result as an array type. It implements
the following interface:

- `\Opengento\Gdpr\Service\Export\ProcessorInterface`

The processors are registered to the following composite, if you want to register you own implementation,
add it to the composite via the `di.xml` file configuration:

```xml
<type name="Opengento\Gdpr\Service\Export\Processor\CompositeProcessor">
    <arguments>
        <argument name="processors" xsi:type="array">
            <item name="customer_data" xsi:type="object">Opengento\Gdpr\Service\Export\Processor\CustomerDataProcessor</item>
            <item name="customer_address_data" xsi:type="object">Opengento\Gdpr\Service\Export\Processor\CustomerAddressDataProcessor</item>
            <item name="quote" xsi:type="object">Opengento\Gdpr\Service\Export\Processor\QuoteDataProcessor</item>
            <item name="order" xsi:type="object">Opengento\Gdpr\Service\Export\Processor\OrderDataProcessor</item>
            <item name="subscriber" xsi:type="object">Opengento\Gdpr\Service\Export\Processor\SubscriberDataProcessor</item>
        </argument>
    </arguments>
</type>
```

The export renderer job is to export a result into a file. It implements the following interface: 

- `\Opengento\Gdpr\Service\Export\RendererInterface`

The renderers are registered to the following factory, if you want to register
your own implementation, add it to the factory via the `di.xml` file configuration:

```xml
<type name="Opengento\Gdpr\Service\Export\RendererFactory">
    <arguments>
        <argument name="renderers" xsi:type="array">
            <item name="json" xsi:type="string">Opengento\Gdpr\Service\Export\Renderer\JsonRenderer</item>
            <item name="csv" xsi:type="string">Opengento\Gdpr\Service\Export\Renderer\CsvRenderer</item>
            <item name="xml" xsi:type="string">Opengento\Gdpr\Service\Export\Renderer\XmlRenderer</item>
            <item name="html" xsi:type="string">Opengento\Gdpr\Service\Export\Renderer\HtmlRenderer</item>
            <item name="pdf" xsi:type="string">Opengento\Gdpr\Service\Export\Renderer\PdfRenderer</item>
        </argument>
    </arguments>
</type>
```

When the renderers are registered in the factory, they are available to use on the administrator configuration view.

## Important

The newsletter integration in Magento does not follows the Service Contract Pattern applied to the Magento 2 core.  
Actually the `Subscriber` model only exists as its own `AbstractModel` and has no preference over an API interface.  
As the existing model is not final, it can be plugged and an interceptor is create on the compilation.  
It has side effect and break the data collector resolver by type hitting.  
That's why the subscriber model is used in composition in our own class and marked as final. All call are delegated to
the original subscriber model (with their plugin and preferences).

`\Opengento\Gdpr\Model\Newsletter\Subscriber` class is the final state of `\Magento\Newsletter\Model\Subscriber`.
