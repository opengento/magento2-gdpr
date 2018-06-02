# GDPR Module for Magento 2

[![Latest Stable Version](https://img.shields.io/packagist/v/opengento/module-gdpr.svg?style=flat-square)](https://packagist.org/packages/opengento/module-gdpr)
[![License: MIT](https://img.shields.io/github/license/opengento/magento2-gdpr.svg?style=flat-square)](./LICENSE) 


Extension allows customers to delete, anonymize, or export their personal data.


## Setup

Magento 2 Open Source or Commerce edition are required.

### Get the package

**Zip Package:**

Unzip the package in app/code/Opengento/Gdpr.

**Composer Package:**

```
composer require opengento/module-gdpr
```

### Install the module

Then, run the following magento command:

```
php bin/magento setup:upgrade
```

**If you are in production mode, do not forget to recompile and redeploy the static resources.**

## Usage

* Configuration for this module is located in 'Stores > Configuration > Customers > Customer Configuration > Privacy (GDPR)'.
* Account deletion, anonymization, and export can be done in 'My Account > Privacy Settings'.
* Customers can export their data in .zip archive containing .csv files with personal, wishlist, quote, and address data.
* Customer can delete or anonymize their account. Current password and reason is required. Account will be deleted within 1 hour (or as specified in configuration), in this time span its possible for customers to undo deletion.
* If customer has made at least one order, they are ineligible to delete their account, instead it will be anonymized.
* When a customer visits your store for the first time, a popup notification about cookie policy will be shown.

## Settings

To configure the module, go to the admin panel, then go to Stores > Configuration > Customer

## Developers

The following documentation explain how to add your own processors to the workflow.

### Extends Export

...todo

### Extends Deletion

...todo

### Extends Anonymization

...todo

## Support

- Raise a new [request](https://github.com/opengento/magento2-gdpr/issues) on the issue tracker.

## Authors

- **Opengento Community** - *Lead* - [They're awesome!](https://github.com/opengento)
- **Contributors** - *Contributor* - [Many thanks!](https://github.com/opengento/magento2-gdpr/graphs/contributors)

## Licence

This project is licensed under the MIT License - see the [LICENSE](./LICENSE) details.

***That's all folks!***
