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
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var EraseSalesInformationInterface
     */
    private $eraseSalesInformation;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        EraseSalesInformationInterface $eraseSalesInformation
    ) {
        $this->orderRepository = $orderRepository;
        $this->eraseSalesInformation = $eraseSalesInformation;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(int $orderId): bool
    {
        $order = $this->orderRepository->get($orderId);
        $lastActive = new DateTime($order->getUpdatedAt());

        if ($this->eraseSalesInformation->isAlive($lastActive)) {
            $this->eraseSalesInformation->scheduleEraseEntity((int) $order->getEntityId(), 'order', $lastActive);

            return true;
        }

        return $this->orderRepository->delete($order);
    }
}
