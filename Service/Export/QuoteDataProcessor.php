<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Flurrybox\EnhancedPrivacy\Service\Export;

use Flurrybox\EnhancedPrivacy\Helper\Data;
use Magento\Quote\Model\QuoteRepository;

/**
 * Class QuoteDataProcessor
 */
class QuoteDataProcessor implements ProcessorInterface
{
    /**
     * @var \Flurrybox\EnhancedPrivacy\Helper\Data
     */
    private $helperData;

    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    private $quoteRepository;

    /**
     * @param \Flurrybox\EnhancedPrivacy\Helper\Data $helperData
     */
    public function __construct(
        Data $helperData,
        QuoteRepository $quoteRepository
    ) {
        $this->helperData = $helperData;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(int $customerId, array $data): array
    {
        $quoteCollection = $this->quoteRepository->getForCustomer($customerId);

        return array_merge_recursive(
            $data,
            ['quotes' => $quoteCollection->toArray()]
        );
    }
}
