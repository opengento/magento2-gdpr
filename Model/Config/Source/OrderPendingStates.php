<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory;

class OrderPendingStates implements OptionSourceInterface
{
    private ?array $options = null;

    public function __construct(private CollectionFactory $collectionFactory) {}

    public function toOptionArray(): array
    {
        return $this->options ??= $this->collectionFactory->create()->joinStates()->toOptionArray();
    }
}
