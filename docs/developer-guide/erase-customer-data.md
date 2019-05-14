# Erase Customer Data

[BACK TO THE MENU](/magento2-gdpr/)

___

This module allows you to define the strategy to apply for the different processors.  
You can configure it thanks to the admin system configuration, but you can also cheat and
define the strategy to apply for them via the `etc/di.xml` file. Be careful, the settings from the configuration
are always checked in top priority. To make it via the code, add your preferences as following:

```xml
<type name="Opengento\Gdpr\Model\Config\ErasureComponentStrategy">
    <arguments>
        <argument name="componentsStrategies" xsi:type="array">
            <item name="component_name_1" xsi:type="const">Opengento\Gdpr\Service\ErasureStrategy::STRATEGY_ANONYMIZE</item>        
            <item name="component_name_2" xsi:type="const">Opengento\Gdpr\Service\ErasureStrategy::STRATEGY_DELETE</item>        
            <item name="component_name_3" xsi:type="string">custom_strategy_code</item>        
        </argument>
    </arguments>
</type>
```

Warning, if you want to implement your own strategy type, you must create your own strategy class object, but you will be able to use the `Opengento\Gdpr\Model\Config\ErasureComponentStrategy` to serve your components by strategy.  
Do not forget to use the right services managers, but you are free to use yours:  
- `Opengento\Gdpr\Service\AnonymizeManagement`
- `Opengento\Gdpr\Service\DeleteManagement`
