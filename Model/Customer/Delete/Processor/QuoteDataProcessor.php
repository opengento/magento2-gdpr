<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Delete\Processor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Quote\Api\CartRepositoryInterface;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;

class QuoteDataProcessor implements ProcessorInterface
{
    private CartRepositoryInterface $quoteRepository;

    private SearchCriteriaBuilder $criteriaBuilder;

    public function __construct(
        CartRepositoryInterface $quoteRepository,
        SearchCriteriaBuilder $criteriaBuilder
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->criteriaBuilder = $criteriaBuilder;
    }

    public function execute(int $customerId): bool
    {
        $this->criteriaBuilder->addFilter('customer_id', $customerId);
        $quoteList = $this->quoteRepository->getList($this->criteriaBuilder->create());

        foreach ($quoteList->getItems() as $quote) {
            $this->quoteRepository->delete($quote);
        }

        return true;
    }
}
