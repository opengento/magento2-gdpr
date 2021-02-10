# GDPR Module for Magento 2

[![Latest Stable Version](https://img.shields.io/packagist/v/opengento/module-gdpr.svg?style=flat-square)](https://packagist.org/packages/opengento/module-gdpr)
[![License: MIT](https://img.shields.io/github/license/opengento/magento2-gdpr.svg?style=flat-square)](./LICENSE) 
[![Packagist](https://img.shields.io/packagist/dt/opengento/module-gdpr.svg?style=flat-square)](https://packagist.org/packages/opengento/module-gdpr/stats)
[![Packagist](https://img.shields.io/packagist/dm/opengento/module-gdpr.svg?style=flat-square)](https://packagist.org/packages/opengento/module-gdpr/stats)
[![Codacy Badge](https://img.shields.io/codacy/grade/e43739589ae249a58b4af6dfcd9c555a?style=flat-square)](https://www.codacy.com/gh/opengento/magento2-gdpr)

This extension fullfill the GDPR requirements for Magento 2.

 - [Setup](#setup)
   - [Composer installation](#composer-installation)
   - [Setup the module](#setup-the-module)
 - [Features](#features)
 - [Settings](#settings)
 - [Documentation](#documentation)
 - [Support](#support)
 - [Authors](#authors)
 - [License](#license)

## Setup

Magento 2 Open Source or Commerce edition is required.

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

## Features

Users, guest and customer can:

* **[Art. 16 GDPR](https://gdpr-info.eu/art-16-gdpr/)** Edit their personal data (native in vanilla)

* **[Art. 17 GDPR](https://gdpr-info.eu/art-17-gdpr/)**
  * Customers can use their 'right to be forgotten'. Account deletion and anonymization can be done in 'My Account > Privacy Settings'.
    The password is required to ensure the customer legibility.
    The account will be erased within 1 hour, or as specified in configuration. The customer can undo the action in this time span.
  * Guest users can use their 'right to be forgotten'. Account deletion and anonymization can be done in the order view,
    they must fill the guest form first to show their order.
    The data will be erased within 1 hour, or as specified in the configuration. The guest can undo the action is this time spare.
  * The customers and guests will be erased after a configurable idle time.
  * The sales information are locked within a configurable time. These information are automatically erased after this period.
  
  As a merchant you can easily manage which type of entity must to be delete or anonymize. In the last case, 
the module allows to define which attribute must to be anonymize, and how it is.

Times are configurable too, you can define the period of cancellation for the erasure, 
the idle time for the users before they are erase, and the sales information lifetime.
  
* **[Art. 20 GDPR](https://gdpr-info.eu/art-20-gdpr/)**
  * Customers can export their data in `.zip` archive containing file, `.html` (many others are available), with personal data.
    Personal data export can be done in 'My Account > Privacy Settings'.
  * Guest users can export their data in `.zip` archive containing file, `.html` (many others are available), with personal data.
    Personal data export can be done in the order view, they must fill the guest form first to show their order.
* Cookie Policy in a disclosure popup are shown at the first time customer visit.

Details:

- [x] Erasure: delete or anonymize specific data thanks to configurable settings in admin ui.
- [x] Configure which order can be erased, regarding their state and life time.
- [x] Privacy data will be automatically erased after a delay.
- [x] Sales data are safely keeped till the preservation delay expired.
- [x] Choose the file name and the format of your choice for the privacy data export.
- [x] Choose which data is interpreted as privacy data and will be exported.
- [x] Actions related to the GDPR compliance are reported in the admin ui.
- [x] Merchants can execute and keep an eye on the performed actions from the admin ui.
- [x] Choose the CMS static block to show on the storefront by scope and features.
- [x] Enable or disable features for the storefront.
- [x] Notify the user when a GDPR action is performed, configure the template and sending settings.
- [x] Display the cookie disclosure pop-in and edit its content as you want.

Languages:

- [x] en_US ; English
- [x] de_DE ; German
- [x] fr_FR ; French
- [x] nl_NL ; Dutch
- [x] it_IT ; Italian

## Settings

The configuration for this module is available in 'Stores > Configuration > GDPR Compliance'.  

## Documentation

The documentation is available [here](https://opengento.fr/magento2-gdpr/).

## Support

Raise a new [request](https://github.com/opengento/magento2-gdpr/issues) to the issue tracker.

## Authors

- **Opengento Community** - *Lead* - [![Twitter Follow](https://img.shields.io/twitter/follow/opengento.svg?style=social)](https://twitter.com/opengento)
- **Thomas Klein** - *Maintainer* - [![GitHub followers](https://img.shields.io/github/followers/thomas-kl1.svg?style=social)](https://github.com/thomas-kl1)
- **Contributors** - *Contributor* - [![GitHub contributors](https://img.shields.io/github/contributors/opengento/magento2-gdpr.svg?style=flat-square)](https://github.com/opengento/magento2-gdpr/graphs/contributors)

## License

This project is licensed under the MIT License - see the [LICENSE](./LICENSE) details.

***That's all folks!***
