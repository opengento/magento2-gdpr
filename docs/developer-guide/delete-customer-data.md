# Delete Customer Data

[BACK TO THE MENU](/magento2-gdpr/)

___

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
