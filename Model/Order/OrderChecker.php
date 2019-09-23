<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Order;

use Magento\Sales\Api\OrderRepositoryInterface;
use Opengento\Gdpr\Model\Config;
use Opengento\Gdpr\Model\Entity\EntityCheckerInterface;

final class OrderChecker implements EntityCheckerInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        Config $config
    ) {
        $this->orderRepository = $orderRepository;
        $this->config = $config;
    }

    public function canErase(int $orderId): bool
    {
        $order = $this->orderRepository->get($orderId);

        return \in_array($order->getState(), $this->config->getAllowedStatesToErase(), true);
    }
}
