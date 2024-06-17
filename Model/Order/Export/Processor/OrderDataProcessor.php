<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Order\Export\Processor;

use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;

class OrderDataProcessor extends AbstractDataProcessor
{
    protected function export(OrderInterface $order, array $data): array
    {
        /** @var Order $order */
        $key = 'order_id_' . $order->getEntityId();
        $data['orders'][$key] = $this->collectData($order);

        /** @var OrderAddressInterface|null $orderAddress */
        foreach ([$order->getBillingAddress(), $order->getShippingAddress()] as $orderAddress) {
            if ($orderAddress) {
                $data['orders'][$key][$orderAddress->getAddressType()] = $this->collectData($orderAddress);
            }
        }

        return $data;
    }
}
