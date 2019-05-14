# Export Customer Data

[BACK TO THE MENU](/magento2-gdpr/)

___

In order to export your custom component, you must create a new processor.  
To create a new processor, you must implement the following interface: `\Opengento\Gdpr\Service\Export\ProcessorInterface`.  
Then, register your processor to the following pool `\Opengento\Gdpr\Service\Export\ProcessorPool`, as described:

```xml
<type name="Opengento\Gdpr\Service\Export\ProcessorPool">
    <arguments>
        <argument name="array" xsi:type="array">
            <item name="my_component" xsi:type="string">Vendor\Module\ExportProcessor</item>
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
            <item name="my_renderer" xsi:type="string">Vendor\Module\ExportRenderer</item>
        </argument>
    </arguments>
</type>
```
