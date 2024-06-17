<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action\PerformedBy;

use Magento\Framework\Registry;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Opengento\Gdpr\Model\Action\PerformedByInterface;

class Guest implements PerformedByInterface
{
    private Registry $coreRegistry;

    private string $attributeName;

    public function __construct(
        Registry $coreRegistry,
        string $attributeName = OrderInterface::CUSTOMER_EMAIL
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->attributeName = $attributeName;
    }

    public function get(): string
    {
        $order = $this->coreRegistry->registry('current_order');

        return $order && $order instanceof Order ? $order->getData($this->attributeName) : '';
    }
}
