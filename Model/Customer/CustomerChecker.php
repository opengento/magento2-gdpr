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
use Opengento\Gdpr\Model\Config;
use Opengento\Gdpr\Model\Entity\EntityCheckerInterface;

final class CustomerChecker implements EntityCheckerInterface
{
    private OrderRepositoryInterface $orderRepository;

    private SearchCriteriaBuilder $criteriaBuilder;

    private Config $config;

    /**
     * @var bool[]
     */
    private array $cache;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $criteriaBuilder,
        Config $config
    ) {
        $this->orderRepository = $orderRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->config = $config;
        $this->cache = [];
    }

    public function canErase(int $customerId): bool
    {
        if (!isset($this->cache[$customerId])) {
            $this->criteriaBuilder->addFilter(OrderInterface::STATUS, $this->config->getAllowedStatusesToErase(), 'nin');
            $this->criteriaBuilder->addFilter(OrderInterface::CUSTOMER_ID, $customerId);
            $orderList = $this->orderRepository->getList($this->criteriaBuilder->create());

            $this->cache[$customerId] = !$orderList->getTotalCount();
        }

        return $this->cache[$customerId];
    }
}
