# Export Customer Data

[BACK TO THE MENU](/magento2-gdpr/)

___

In order to export your custom component, you must create a new processor.  
To create a new processor, you must implement the following interface: `\Opengento\Gdpr\Service\Export\ProcessorInterface`.  
Then, register your processor to the following composite `\Opengento\Gdpr\Service\Export\Processor\CompositeProcessor`, as described:

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

You can also create your custom export renderer to make it as be like you want to be.  
To achieve this, you must implement the following interface: `\Opengento\Gdpr\Service\Export\RendererInterface`  
Then, register your renderer to the following factory `\Opengento\Gdpr\Service\Export\RendererFactory`, as described:

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
