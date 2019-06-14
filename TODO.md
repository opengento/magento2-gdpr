# ToDo

- User Guide / Publish / Marketplace

## Processors

Area:

- Analytics
- Wishlist
- Invoice
- Shipment
- CreditMemo
- Refund
- Payment
- Cookie
- Others

## Export

- Payment Data
- Invitation Data
- Multiple export in command line result in filename expanded at each iteration

## Anonymizer
 
- Payment Data
- Invitation Data

## Config

- Improve UI of fields: attributes list instead of text field

## Doc

- Add images
- Complete developer guide
- Attributes, Custom Attributes, Extensible Data

## Facade

GDPR facade for customer actions

## Guest

Erase:
- Processor: deeper
- Strategy Scope: following:

```php
$processor = $this->processorFactory->get($this->metadata->getComponentProcessor($component));
```
