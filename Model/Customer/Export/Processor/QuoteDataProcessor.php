<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Export\Processor;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Opengento\Gdpr\Model\Entity\DataCollectorInterface;
use Opengento\Gdpr\Service\Export\Processor\AbstractDataProcessor;

final class QuoteDataProcessor extends AbstractDataProcessor
{
    private CartRepositoryInterface $quoteRepository;

    private SearchCriteriaBuilder $criteriaBuilder;

    public function __construct(
        CartRepositoryInterface $quoteRepository,
        SearchCriteriaBuilder $criteriaBuilder,
        DataCollectorInterface $dataCollector
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        parent::__construct($dataCollector);
    }

    public function execute(int $customerId, array $data): array
    {
        $this->criteriaBuilder->addFilter('customer_id', $customerId);
        $quoteList = $this->quoteRepository->getList($this->criteriaBuilder->create());

        /** @var Quote $quote */
        foreach ($quoteList->getItems() as $quote) {
            $key = 'quote_id_' . $quote->getId();
            $data['quotes'][$key] = $this->collectData($quote);

            /** @var Address|null $quoteAddress */
            foreach ([$quote->getBillingAddress(), $quote->getShippingAddress()] as $quoteAddress) {
                if ($quoteAddress) {
                    $data['quotes'][$key][$quoteAddress->getAddressType()] = $this->collectData($quoteAddress);
                }
            }
        }

        return $data;
    }
}
