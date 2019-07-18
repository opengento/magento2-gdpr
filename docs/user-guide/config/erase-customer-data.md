# Erase Customer Data

[BACK TO THE MENU](/magento2-gdpr/)

___

* [General Settings](/magento2-gdpr/user-guide/config/general#settings)
* Erasure Settings
* [Export Settings](/magento2-gdpr/user-guide/config/export-customer-data#settings)
* [Cookie Settings](/magento2-gdpr/user-guide/config/cookie-disclosure#settings)

## Settings

***Stores > Settings > Configuration > Customers > GDPR Compliance > Right To Erasure***

  * Erasure
    > It allows to enable or disable the customer data erasure feature at any moment.
  * Erasure Time Laps
    > It allows to define the time back to cancel the erasure.
  * Cron Schedule
    > It allows to define the cron schedule which execute the erasures.
  * Information
    > It allows to define the CMS static block to link to the right to erasure policy.
  * Erase Components Processors
    > It allows to define the erasure processor (`Anonymize` or `Delete`) to apply to each available components.
  * Anonymization
    > This section has only interests if the default erasure strategy is set to `Anonymize`.
    * Information
      > It allows to define the CMS static block to link to the anonymization policy.
    * Delete Customer if no Orders
      > It allows to define if the customer must be removed if he has no processed orders.
