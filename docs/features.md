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

 - [Index](/magento2-gdpr/)
 - [Setup](/magento2-gdpr/setup)

---

![config](/magento2-gdpr/images/config.png)

- A privacy disclosure page and blocks are mandatory

![general](/magento2-gdpr/images/general.png)

## Right to be forgotten:

### As a merchant:

- I can decide what entity should remain and be anonymized, and what should be deleted

![erasure](/magento2-gdpr/images/erasure-1.png)

***Registered Customer erasure rule:***

![erasure](/magento2-gdpr/images/erasure-2.png)

***Guest Customer erasure rule:***

![erasure](/magento2-gdpr/images/erasure-3.png)

***Entities anonymisation rules:***

![anonymisation](/magento2-gdpr/images/erasure-anonymisation.png)

***Customer entity anonymisation rule:***

![anonymize customer](/magento2-gdpr/images/erasure-anonymisation-customer.png)

### As a guest customer:

- I can request the removal of the personal data on the order view

### As a registered customer:

- I can request the removal of the personal data on the account view

![export customer](/magento2-gdpr/images/user-privacy-settings.png)

![export customer](/magento2-gdpr/images/user-erase-confirm.png)

![export customer](/magento2-gdpr/images/user-erase-confirm2.png)

![export customer](/magento2-gdpr/images/user-erase-pending.png)

![export customer](/magento2-gdpr/images/user-erase-cancel.png)

### Automatic process

- Personal data are removed automatically after they have reach their end of life period.
  However the sales data related to the personal data are kept until they have reach their own life time period.
  (Sales data can be preserved longer than personal data for financial and accountancy reasons)

## Right to data portability

### As a merchant:

- I can decide what entities and their attributes are exported

![export](/magento2-gdpr/images/export.png)

***Entities export rules:***

![export entities](/magento2-gdpr/images/export-entities.png)

***Customer entity export rule:***

![export customer](/magento2-gdpr/images/export-customer.png)

### As a registered customer:

![export customer](/magento2-gdpr/images/user-privacy-settings.png)

***Request a data portability***

![export customer](/magento2-gdpr/images/user-export-pending.png)

***Download the archive***

![export customer](/magento2-gdpr/images/user-export-download.png)

## Cookie Disclosure

### As a merchant:

- I can decide what to show

![cookie](/magento2-gdpr/images/cookie.png)

### As a visitor:

![notification](/magento2-gdpr/images/user-cookie.png)

## GDPR Actions Notification

### As a merchant:

- I can override the templates

![notification](/magento2-gdpr/images/notification.png)

***Notification of an erasure:***

![erase notification](/magento2-gdpr/images/notification-erase.png)

***Notification of a pending erasure:***

![pending erase notification](/magento2-gdpr/images/notification-erase-pending.png)

***Notification of data portability request:***

![export notification](/magento2-gdpr/images/notification-export.png)

## GDPR Log Actions

### As a merchant:

- I can review all performed actions related to GDPR actions, such as ask for removal, ask for data portability...

![action log](/magento2-gdpr/images/action-log.png)
