<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Opengento\Gdpr\Model\Config\Entity\Erasure as ErasureConfig;
use Opengento\Gdpr\Model\Entity\EntityCheckerInterface;

class CustomerChecker implements EntityCheckerInterface
{
    public function __construct(
        private CollectionFactory $collectionFactory,
        private ErasureConfig $erasureConfig
    ) {}

    public function canErase(int $entityId): bool
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(OrderInterface::CUSTOMER_ID, $entityId);
        $collection->addFieldToFilter(OrderInterface::STATE, ['nin' => $this->erasureConfig->getAllowedStatesToErase()]);//ToDo scope Website

        return $collection->getSize() > 0;
    }
}
