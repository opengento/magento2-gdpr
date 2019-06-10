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

* Developer Guide
* [Erase Customer Data](/magento2-gdpr/developer-guide/erase-customer-data)
    * [Anonymize Customer Data](/magento2-gdpr/developer-guide/anonymize-customer-data)
    * [Delete Customer Data](/magento2-gdpr/developer-guide/delete-customer-data)
* [Export Customer Data](/magento2-gdpr/developer-guide/export-customer-data)

### How to extends/plug class and methods

The module try to be as compliant as possible to the SOLID principles.  
In that way the code is open to extensibility, but closed to modification.  

A concrete class should be write as it's its final state. That's why almost all class are final.  
However you have noticed that Magento 2 frameworks require that the class must not be final if you
want to add a plugin over it. Actually you should not use the Magento plugin feature if possible, in any case
(but it's an another talk ¯\\_(ツ)_/¯ ). 

- **How to extend controllers in Magento 2**

A controller action should never be override with a di preference!  
Please use the `routes.xml` configuration file, it allows you to add controllers and actions to an existing route.   
In that case you can fetch your action before or after the original one (or specified one). Your action is executed,
as defined, after or before. In the first case, if the existing action does not returns a `\Magento\Framework\Controller\ResultInterface`
or `\Magento\Framework\App\ResponseInterface` object, your action is called.  

By default it's suggested to declare your module in a route with `after`, so if Magento introduce new actions,
these actions are free of your modifications.
If you need to replace an action, use the `before` declaration, then returns a valid response.  
If you want to change the response of an action, use the `before` declaration too, but use the original action in your own,
call the `execute` method and apply your changes, then returns the result.

- **How to extend class in Magento 2**

Plugins and preferences are not needed here to override and extends the GDPR module core code.  
Actually, you should apply patterns to achieve it.

The pool pattern already allows you to override the class of your choice.  
However you wont be able to extends the existing class, because of the "final" keyword. Indeed, you need to create your 
own class which implements the same interface. Then, simply add the class you want to "extends" as a composition. You will be able to 
exploit the result and override it in your method.

Eg: 
```php
interface I { public function execute(array $data): array; }
final class A implements I { public function execute(array $data): array { //process $data } }

final class B implements I {
    private $a;
    
    public function __construct(A $a) { $this->a = $a; }
    
    public function execute(array $data): array
    {
        $resultA = $this->a->execute($data);

        $resultB = $resultA; // transform $resultA
        
        return $resultB;
    }
}
```
Then:  
```xml
<type name="Pool">
    <arguments>
        <argument name="array" xsi:type="array">
            <argument name="a" xsi:type="string">A</argument>        
        </argument>
    </arguments>
</type>
```
Override by:  
```xml
<type name="Pool">
    <arguments>
        <argument name="array" xsi:type="array">
            <argument name="a" xsi:type="string">B</argument>        
        </argument>
    </arguments>
</type>
```
Congrats! You have overridden class A without extending it!
