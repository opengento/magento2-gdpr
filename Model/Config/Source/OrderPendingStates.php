<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory;

/**
 * Class OrderPendingStates
 */
final class OrderPendingStates implements OptionSourceInterface
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var string[][]
     */
    private $options;

    /**
     * @param \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        return $this->options ?? $this->options = $this->collectionFactory->create()->joinStates()->toOptionArray();
    }
}
