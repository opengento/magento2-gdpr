<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Order\Delete\Processor;

use DateTime;
use Exception;
use Magento\Sales\Api\OrderRepositoryInterface;
use Opengento\Gdpr\Api\EraseSalesInformationInterface;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;

final class OrderDataProcessor implements ProcessorInterface
{
    private OrderRepositoryInterface $orderRepository;

    private EraseSalesInformationInterface $salesInformation;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        EraseSalesInformationInterface $salesInformation
    ) {
        $this->orderRepository = $orderRepository;
        $this->salesInformation = $salesInformation;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(int $orderId): bool
    {
        $order = $this->orderRepository->get($orderId);
        $lastActive = new DateTime($order->getUpdatedAt());

        if ($this->salesInformation->isAlive($lastActive)) {
            $this->salesInformation->scheduleEraseEntity((int) $order->getEntityId(), 'order', $lastActive);

            return true;
        }

        return $this->orderRepository->delete($order);
    }
}
