<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Guest\Export\Processor;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class OrderDataProcessor
 */
final class OrderDataProcessor extends AbstractDataProcessor
{
    /**
     * @inheritdoc
     */
    public function execute(OrderInterface $order, array $data): array
    {
        /** @var \Magento\Sales\Model\Order $order */
        $key = 'order_id_' . $order->getEntityId();
        $data['orders'][$key] = $this->collectData($order);

        /** @var \Magento\Sales\Api\Data\OrderAddressInterface|null $orderAddress */
        foreach ([$order->getBillingAddress(), $order->getShippingAddress()] as $orderAddress) {
            if ($orderAddress) {
                $data['orders'][$key][$orderAddress->getAddressType()] = $this->collectData($orderAddress);
            }
        }

        return $data;
    }
}
