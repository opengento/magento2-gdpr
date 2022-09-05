<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Anonymize\Processor;

use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ResourceModel\Quote\Address;
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;

final class QuoteDataProcessor implements ProcessorInterface
{
    private AnonymizerInterface $anonymizer;

    private CartRepositoryInterface $quoteRepository;

    private Address $resourceModel;

    private SearchCriteriaBuilder $criteriaBuilder;

    public function __construct(
        AnonymizerInterface $anonymizer,
        CartRepositoryInterface $quoteRepository,
        Address $resourceModel,
        SearchCriteriaBuilder $criteriaBuilder
    ) {
        $this->anonymizer = $anonymizer;
        $this->quoteRepository = $quoteRepository;
        $this->resourceModel = $resourceModel;
        $this->criteriaBuilder = $criteriaBuilder;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(int $customerId): bool
    {
        $this->criteriaBuilder->addFilter('customer_id', $customerId);
        $quoteList = $this->quoteRepository->getList($this->criteriaBuilder->create());

        /** @var Quote $quote */
        foreach ($quoteList->getItems() as $quote) {
            $this->quoteRepository->save($this->anonymizer->anonymize($quote));

            /** @var Quote\Address|null $quoteAddress */
            foreach ([$quote->getBillingAddress(), $quote->getShippingAddress()] as $quoteAddress) {
                if ($quoteAddress) {
                    $this->resourceModel->save($this->anonymizer->anonymize($quoteAddress));
                }
            }
        }

        return true;
    }
}
