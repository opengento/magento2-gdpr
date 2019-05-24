# Erase Customer Data

[BACK TO THE MENU](/magento2-gdpr/)

___

This module allows you to define the strategy to apply for the different processors.  
You can configure it thanks to the admin system configuration, but you can also cheat and
define the strategy to apply for them via the `etc/di.xml` file. Be careful, the settings from the configuration
are always checked in top priority. To make it via the code, add your preferences as following:

```xml
<type name="Opengento\Gdpr\Service\Erase\EraseFactory">
    <arguments>
        <argument name="processors" xsi:type="array">
            <item name="anonymize" xsi:type="string">Opengento\Gdpr\Service\Erase\Processor\AnonymizeProcessor</item>
            <item name="delete" xsi:type="string">Opengento\Gdpr\Service\Erase\Processor\DeleteProcessor</item>        
            <item name="your_custom_strategy" xsi:type="string">Vendor\Module\EraseProcessor</item>        
        </argument>
    </arguments>
</type>
```
