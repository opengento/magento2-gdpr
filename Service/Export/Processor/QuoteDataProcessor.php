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
use Magento\Quote\Api\CartRepositoryInterface;
use Opengento\Gdpr\Service\Export\ProcessorInterface;

/**
 * Class QuoteDataProcessor
 */
class QuoteDataProcessor implements ProcessorInterface
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\EntityManager\Hydrator
     */
    private $hydrator;

    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\EntityManager\Hydrator $hydrator
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Hydrator $hydrator
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->hydrator = $hydrator;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(int $customerId, array $data): array
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('customer_id', $customerId);
        $quoteList = $this->quoteRepository->getList($searchCriteria->create());
        $data['quotes'] = $this->generateArray($quoteList);

        return $data;
    }

    /**
     * Collect the customer quotes data to export
     *
     * @param \Magento\Framework\Api\SearchResultsInterface $searchResults
     * @return array
     */
    private function generateArray(SearchResultsInterface $searchResults): array
    {
        $data = [];

        /** @var \Magento\Quote\Api\Data\CartInterface $entity */
        foreach ($searchResults->getItems() as $entity) {
            $data[$entity->getId()] = $this->hydrator->extract($entity);
        }

        return $data;
    }
}
