<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

/**
 * Class CustomerChecker
 */
final class CustomerChecker
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var bool[]
     */
    private $cache;

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->cache = [];
    }

    /**
     * Check if the customer has pending orders
     *
     * @param int $customerId
     * @param bool $forceReload [optional]
     * @return bool
     */
    public function hasPendingOrders(int $customerId, bool $forceReload = false): bool
    {
        if ($forceReload || !isset($this->cache[$customerId])) {
            $this->searchCriteriaBuilder->addFilter(
                OrderInterface::STATE,
                [Order::STATE_CANCELED, Order::STATE_CLOSED, Order::STATE_COMPLETE],
                'nin'
            );
            $this->searchCriteriaBuilder->addFilter(OrderInterface::CUSTOMER_ID, $customerId);
            $orderList = $this->orderRepository->getList($this->searchCriteriaBuilder->create());

            $this->cache[$customerId] = (bool) $orderList->getTotalCount();
        }

        return $this->cache[$customerId];
    }
}
