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

/**
 * Class OrderChecker
 */
final class OrderChecker implements EntityCheckerInterface
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Opengento\Gdpr\Model\Config $config
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        Config $config
    ) {
        $this->orderRepository = $orderRepository;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function canErase(int $orderId, bool $forceReload = false): bool
    {
        $order = $this->orderRepository->get($orderId);

        return !$order->getCustomerId() && \in_array($order->getState(), $this->config->getAllowedStatesToErase(), true);
    }
}
