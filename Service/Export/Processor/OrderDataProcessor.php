<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Processor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\EntityManager\Hydrator;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Opengento\Gdpr\Service\Export\ProcessorInterface;

/**
 * Class QuoteDataProcessor
 */
final class OrderDataProcessor implements ProcessorInterface
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
     * @var \Magento\Framework\EntityManager\Hydrator
     */
    private $hydrator;

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\EntityManager\Hydrator $hydrator
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Hydrator $hydrator
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->hydrator = $hydrator;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(int $customerId, array $data): array
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(OrderInterface::CUSTOMER_ID, $customerId);
        $orderList = $this->orderRepository->getList($searchCriteria->create());
        $data['orders'] = $this->generateArray($orderList);

        return $data;
    }

    /**
     * Collect the customer orders data to export
     *
     * @param \Magento\Framework\Api\SearchResultsInterface $searchResults
     * @return array
     */
    private function generateArray(SearchResultsInterface $searchResults): array
    {
        $data = [];

        /** @var \Magento\Sales\Api\Data\OrderInterface $entity */
        foreach ($searchResults->getItems() as $entity) {
            $data[$entity->getEntityId()] = $this->hydrator->extract($entity);
        }

        return $data;
    }
}
