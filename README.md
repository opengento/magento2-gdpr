# GDPR Module for Magento 2

[![Latest Stable Version](https://img.shields.io/packagist/v/opengento/module-gdpr.svg?style=flat-square)](https://packagist.org/packages/opengento/module-gdpr)
[![License: MIT](https://img.shields.io/github/license/opengento/magento2-gdpr.svg?style=flat-square)](./LICENSE) 
[![Packagist](https://img.shields.io/packagist/dt/opengento/module-gdpr.svg?style=flat-square)](https://packagist.org/packages/opengento/module-gdpr)
[![Packagist](https://img.shields.io/packagist/dm/opengento/module-gdpr.svg?style=flat-square)](https://packagist.org/packages/opengento/module-gdpr)

This extension allows users to delete, anonymize, and export their personal data.

 - [Setup](#setup)
   - [Composer installation](#composer-installation)
   - [Setup the module](#setup-the-module)
 - [Usage](#usage)
 - [Settings](#settings)
 - [Developers](#developers)
 - [Support](#support)
 - [Authors](#authors)
 - [License](#license)

## Setup

Magento 2 Open Source or Commerce edition is required.

- The version `3.x` is compliant with Magento `2.3.x`.  
- The version `2.x` is compliant with Magento `2.2.x`.

This module does not support Magento `2.0.x` and `2.1.x`, as these versions ar not anymore maintained.  

**The next stable release will break all previous versions, and will be published as the following tag: `100.0.0`**

###  Composer installation

Run the following composer command:

```
composer require opengento/module-gdpr
```

### Setup the module

Run the following magento command:

```
bin/magento setup:upgrade
```

**If you are in production mode, do not forget to recompile and redeploy the static resources.**

## Usage

* **[Art. 17 GDPR](https://gdpr-info.eu/art-17-gdpr/)**
  * Customers can use their 'right to be forgotten'. Account deletion and anonymization can be done in 'My Account > Privacy Settings'.
    The password is required to ensure the customer legibility.
    The account will be erased within 1 hour, or as specified in configuration. The customer can undo the action in this time span.
  * Guest users can use their 'right to be forgotten'. Account deletion and anonymization can be done in the order view,
    they must fill the guest form first to show their order.
    The data will be erased within 1 hour, or as specified in the configuration. The guest can undo the action is this time spare.
  * The customers and guests will be erased after a configurable idle time.
  * The sales information are locked within a configurable time. These information are automatically erased after this period.
* **[Art. 20 GDPR](https://gdpr-info.eu/art-20-gdpr/)**
  * Customers can export their data in `.zip` archive containing file, `.html` (many others are available), with personal data.
    Personal data export can be done in 'My Account > Privacy Settings'.
  * Guest users can export their data in `.zip` archive containing file, `.html` (many others are available), with personal data.
    Personal data export can be done in the order view, they must fill the guest form first to show their order.
* Cookie Policy in a disclosure popup are shown at the first time customer visit.

As a merchant you can easily manage which type of entity must to be delete or anonymize. In the last case, 
the module allows to define which attribute must to be anonymize, and how it is.

Times are configurable too, you can define the period of cancellation for the erasure, 
the idle time for the users before they are erase, and the sales information lifetime.

## Settings

The configuration for this module is located in 'Stores > Configuration > Customers > Customer Configuration > Privacy (GDPR)'.  
The whole documentation and guide is available at [our website](https://opengento.fr/magento2-gdpr/).

## Developers

The developer documentation is available at [our website](https://opengento.fr/magento2-gdpr/).  
It explains how to add your own processors to the GDPR workflow.

## Support

Raise a new [request](https://github.com/opengento/magento2-gdpr/issues) to the issue tracker.  
Please provide your Magento 2 version and the module version. Explain how to reproduce your issue and what's expected.

## Authors

- **Initial Inspiration** - *`Cookie PopUp` sources* - [flurrybox](https://github.com/flurrybox)
- **Opengento Community** - *Lead* - [![Twitter Follow](https://img.shields.io/twitter/follow/opengento.svg?style=social)](https://twitter.com/opengento)
- **Thomas Klein** - *Maintainer* - [![GitHub followers](https://img.shields.io/github/followers/thomas-kl1.svg?style=social)](https://github.com/thomas-kl1)
- **Contributors** - *Contributor* - [![GitHub contributors](https://img.shields.io/github/contributors/opengento/magento2-gdpr.svg?style=flat-square)](https://github.com/opengento/magento2-gdpr/graphs/contributors)

## License

This project is licensed under the MIT License - see the [LICENSE](./LICENSE) details.

***That's all folks!***
