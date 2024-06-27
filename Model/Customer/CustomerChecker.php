<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Opengento\Gdpr\Model\Config;
use Opengento\Gdpr\Model\Entity\EntityCheckerInterface;

class CustomerChecker implements EntityCheckerInterface
{
    public function __construct(
        private CollectionFactory $collectionFactory,
        private Config $config
    ) {}

    public function canErase(int $customerId): bool
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(OrderInterface::CUSTOMER_ID, $customerId);
        $collection->addFieldToFilter(OrderInterface::STATE, ['nin' => $this->config->getAllowedStatesToErase()]);

        return $collection->getSize() > 0;
    }
}
