# Anonymize Customer Data

[BACK TO THE MENU](/magento2-gdpr/)

___

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
