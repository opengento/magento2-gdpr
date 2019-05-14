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

### How to override class and methods

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
