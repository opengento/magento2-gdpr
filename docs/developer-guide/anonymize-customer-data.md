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

## Important

The newsletter integration in Magento does not follows the Service Contract Pattern applied to the Magento 2 core.  
Actually the `Subscriber` model only exists as its own `AbstarctModel` and has no preference over an API interface.  
As the existing model is not final, it can be plugged and an interceptor is generation on the compilation.  
It has for side effect to break the data collector resolve by type hitting.  
That's why the subscriber model is extender in our own class and marked as final.

`\Opengento\Gdpr\Model\Newsletter\Subscriber` class is the final state of `\Magento\Newsletter\Model\Subscriber`.
