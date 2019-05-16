<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Processor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Quote\Api\CartRepositoryInterface;
use Opengento\Gdpr\Model\Entity\DataCollectorInterface;

/**
 * Class QuoteDataProcessor
 */
final class QuoteDataProcessor extends AbstractDataProcessor
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
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Opengento\Gdpr\Model\Entity\DataCollectorInterface $dataCollector
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataCollectorInterface $dataCollector
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($dataCollector);
    }

    /**
     * @inheritdoc
     */
    public function execute(int $customerId, array $data): array
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('customer_id', $customerId);
        $quoteList = $this->quoteRepository->getList($searchCriteria->create());

        /** @var \Magento\Quote\Api\Data\CartInterface $entity */
        foreach ($quoteList->getItems() as $entity) {
            $data['quotes']['quote_id_' . $entity->getId()] = $this->collectData($entity);
        }

        return $data;
    }
}
