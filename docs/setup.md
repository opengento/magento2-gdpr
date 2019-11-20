# Setup

[![Latest Stable Version](https://img.shields.io/packagist/v/opengento/module-gdpr.svg?style=flat-square)](https://packagist.org/packages/opengento/module-gdpr)
[![License: MIT](https://img.shields.io/github/license/opengento/magento2-gdpr.svg?style=flat-square)](./LICENSE)

[![Packagist](https://img.shields.io/packagist/dt/opengento/module-gdpr.svg?style=flat-square)](https://packagist.org/packages/opengento/module-gdpr)
[![Packagist](https://img.shields.io/packagist/dm/opengento/module-gdpr.svg?style=flat-square)](https://packagist.org/packages/opengento/module-gdpr)

[![GitHub forks](https://img.shields.io/github/forks/opengento/magento2-gdpr.svg?style=social)](https://github.com/opengento/magento2-gdpr/network/members)
[![GitHub stars](https://img.shields.io/github/stars/opengento/magento2-gdpr.svg?style=social)](https://github.com/opengento/magento2-gdpr/stargazers)
[![GitHub watchers](https://img.shields.io/github/watchers/opengento/magento2-gdpr.svg?style=social)](https://github.com/opengento/magento2-gdpr/watchers)

___

Magento 2 Open Source or Commerce edition is required.

| Source Tag Version | Magento Version|
| :---               | :---           |
| 3.x                | 2.3.x          |
| 2.x[^deprVersion]  | 2.2.x          |
| 1.x[^deprVersion]  | 2.1.x          |
| N/A[^deprVersion]  | 2.0.x          |

[^deprVersion]: This module does not support the specified Magento version, as it's not anymore maintained.  

### Download the package

**Zip Package:**

[Download ZIP](https://github.com/opengento/magento2-gdpr/archive/master.zip)

Unzip the package in app/code/Opengento/Gdpr.

**Composer Package:**

```
composer require opengento/module-gdpr dev-master
```

### Install the module

Then, run the following magento command:

```
bin/magento setup:upgrade [--keep-generated]
```

**If you are in production mode, do not forget to recompile and redeploy the static resources.**

### What's next?

You should refer to the [user-guide](/magento2-gdpr/user-guide/) and learn how to configure and enable the module.  
You could also check the [developer guide](/magento2-gdpr/developer-guide/) for documentation and customization purposes.
