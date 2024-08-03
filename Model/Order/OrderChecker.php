<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Order;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Opengento\Gdpr\Model\Config\Entity\Erasure as ErasureConfig;
use Opengento\Gdpr\Model\Entity\EntityCheckerInterface;

use function in_array;

class OrderChecker implements EntityCheckerInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private StoreManagerInterface $storeManager,
        private ErasureConfig $erasureConfig
    ) {}

    /**
     * @throws NoSuchEntityException
     */
    public function canErase(int $entityId): bool
    {
        $order = $this->orderRepository->get($entityId);

        return in_array($order->getState(), $this->allowedStates($order), true);
    }

    /**
     * @throws NoSuchEntityException
     */
    private function allowedStates(OrderInterface $order): array
    {
        return $this->erasureConfig->getAllowedStatesToErase($this->resolveWebsiteId($order));
    }

    /**
     * @throws NoSuchEntityException
     */
    private function resolveWebsiteId(OrderInterface $order): int
    {
        return (int)$this->storeManager->getStore($order->getStoreId())->getWebsiteId();
    }
}
