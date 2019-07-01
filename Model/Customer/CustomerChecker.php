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

/**
 * Class CustomerChecker
 */
final class CustomerChecker implements EntityCheckerInterface
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
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @var bool[]
     */
    private $cache;

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Opengento\Gdpr\Model\Config $config
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Config $config
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->config = $config;
        $this->cache = [];
    }

    /**
     * @inheritdoc
     */
    public function canErase(int $customerId, bool $forceReload = false): bool
    {
        if ($forceReload || !isset($this->cache[$customerId])) {
            $this->searchCriteriaBuilder->addFilter(OrderInterface::STATE, $this->config->getAllowedStatesToErase(), 'nin');
            $this->searchCriteriaBuilder->addFilter(OrderInterface::CUSTOMER_ID, $customerId);
            $orderList = $this->orderRepository->getList($this->searchCriteriaBuilder->create());

            $this->cache[$customerId] = (bool) $orderList->getTotalCount();
        }

        return $this->cache[$customerId];
    }
}
