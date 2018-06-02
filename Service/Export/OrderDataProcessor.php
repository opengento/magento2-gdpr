<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Flurrybox\EnhancedPrivacy\Service\Export;

use Flurrybox\EnhancedPrivacy\Helper\Data;
use Magento\Sales\Model\OrderRepository;

/**
 * Class QuoteDataProcessor
 */
class OrderDataProcessor implements ProcessorInterface
{
    /**
     * @var \Flurrybox\EnhancedPrivacy\Helper\Data
     */
    private $helperData;

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    private $orderRepository;

    /**
     * @param \Flurrybox\EnhancedPrivacy\Helper\Data $helperData
     */
    public function __construct(
        Data $helperData,
        OrderRepository $orderRepository
    ) {
        $this->helperData = $helperData;
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(int $customerId, array $data): array
    {
        $orderCollection = $this->orderRepository->getList()->addFieldToFilter('customer_id', $customerId);

        return array_merge_recursive(
            $data,
            ['orders' => $orderCollection->toArray()]
        );
    }
}
