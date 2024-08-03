<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Order\SourceProvider;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Opengento\Gdpr\Model\Config\Entity\Erasure as ErasureConfig;
use Opengento\Gdpr\Model\Entity\SourceProvider\ModifierInterface;

class GuestFilterModifier implements ModifierInterface
{
    public function __construct(private ErasureConfig $erasureConfig) {}

    public function apply(AbstractDb $collection, WebsiteInterface $website): void
    {
        $collection->addFieldToFilter(OrderInterface::CUSTOMER_ID, ['null' => true]);
        $collection->addFieldToFilter(OrderInterface::CUSTOMER_IS_GUEST, ['eq' => 1]);
        $collection->addFieldToFilter(
            OrderInterface::STATE,
            ['in' => $this->erasureConfig->getAllowedStatesToErase($website->getId())]
        );
    }
}
