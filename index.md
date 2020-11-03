# GDPR Module for Magento 2

[![Latest Stable Version](https://img.shields.io/packagist/v/opengento/module-gdpr.svg?style=flat-square)](https://packagist.org/packages/opengento/module-gdpr)
[![License: MIT](https://img.shields.io/github/license/opengento/magento2-gdpr.svg?style=flat-square)](./LICENSE)

[![Packagist](https://img.shields.io/packagist/dt/opengento/module-gdpr.svg?style=flat-square)](https://packagist.org/packages/opengento/module-gdpr)
[![Packagist](https://img.shields.io/packagist/dm/opengento/module-gdpr.svg?style=flat-square)](https://packagist.org/packages/opengento/module-gdpr)

[![GitHub forks](https://img.shields.io/github/forks/opengento/magento2-gdpr.svg?style=social)](https://github.com/opengento/magento2-gdpr/network/members)
[![GitHub stars](https://img.shields.io/github/stars/opengento/magento2-gdpr.svg?style=social)](https://github.com/opengento/magento2-gdpr/stargazers)
[![GitHub watchers](https://img.shields.io/github/watchers/opengento/magento2-gdpr.svg?style=social)](https://github.com/opengento/magento2-gdpr/watchers)

___

This module aims to set Magento 2 GDPR ready and compliant.  
It allows the customers to delete, and/or anonymize, and/or export their personal data.

 - [GDPR Compliance Scope](#gdpr-compliance-scope)
 - [Setup](/magento2-gdpr/setup)
 - [Features](/magento2-gdpr/features)
 - [Community](#community)
   - [Authors & Contributors](#authors-&-contributors)
   - [Similar Magento 2 GDPR Module](#similar-magento-2-gdpr-module)
 - [Support](#support)

## GDPR Compliance Scope

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

- [ ] Erasure: delete or anonymize specific data thanks to configurable settings in admin ui.
- [ ] Configure which order can be erased, regarding their state and life time.
- [ ] Privacy data will be automatically erased after a delay.
- [ ] Sales data are safely keeped till the preservation delay expired.
- [ ] Choose the file name and the format of your choice for the privacy data export.
- [ ] Choose which data is interpreted as privacy data and will be exported.
- [ ] Actions related to the GDPR compliance are reported in the admin ui.
- [ ] Merchants can execute and keep an eye on the performed actions from the admin ui.
- [ ] Choose the CMS static block to show on the storefront by scope and features.
- [ ] Enable or disable features for the storefront.
- [ ] Notify the user when a GDPR action is performed, configure the template and sending settings.
- [ ] Display the cookie disclosure pop-in and edit its content as you want.

## Community

This module could not exists without you (and the GDPR law).

### Authors & Contributors

- **Opengento Community** - *Lead* - [![Twitter Follow](https://img.shields.io/twitter/follow/opengento.svg?style=social)](https://twitter.com/opengento)
- **Thomas Klein** - *Maintainer* - [![GitHub followers](https://img.shields.io/github/followers/thomas-kl1.svg?style=social)](https://github.com/thomas-kl1)
- **Contributors** - *Contributor* - [![GitHub contributors](https://img.shields.io/github/contributors/opengento/magento2-gdpr.svg?style=flat-square)](https://github.com/opengento/magento2-gdpr/graphs/contributors)

### Similar Magento 2 GDPR Module

- https://github.com/mageplaza/magento-2-gdpr
- https://github.com/staempfli/magento2-module-gdpr
- https://github.com/flurrybox/enhanced-privacy

## Settings

The configuration for this module is available in 'Stores > Configuration > GDPR Compliance'.  

## Support

[![GitHub issues](https://img.shields.io/github/issues-raw/opengento/magento2-gdpr.svg?style=flat-square)](https://github.com/opengento/magento2-gdpr/issues)
[![GitHub closed issues](https://img.shields.io/github/issues-closed-raw/opengento/magento2-gdpr.svg?style=flat-square)](https://github.com/opengento/magento2-gdpr/issues?q=is%3Aissue+is%3Aclosed)

Raise a new [request](https://github.com/opengento/magento2-gdpr/issues) to the issue tracker.  
Please provide your Magento 2 version and the module version. Explain how to reproduce your issue and what's expected.

## Authors

- **Opengento Community** - *Lead* - [![Twitter Follow](https://img.shields.io/twitter/follow/opengento.svg?style=social)](https://twitter.com/opengento)
- **Thomas Klein** - *Maintainer* - [![GitHub followers](https://img.shields.io/github/followers/thomas-kl1.svg?style=social)](https://github.com/thomas-kl1)
- **Contributors** - *Contributor* - [![GitHub contributors](https://img.shields.io/github/contributors/opengento/magento2-gdpr.svg?style=flat-square)](https://github.com/opengento/magento2-gdpr/graphs/contributors)

## License

This project is licensed under the MIT License - see the [LICENSE](./LICENSE) details.

***That's all folks!***
