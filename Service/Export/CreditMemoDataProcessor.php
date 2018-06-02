<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Flurrybox\EnhancedPrivacy\Service\Export;

use Flurrybox\EnhancedPrivacy\Helper\Data;
use Magento\Sales\Model\Order\CreditmemoRepository;

/**
 * Class CreditMemoDataProcessor
 */
class CreditMemoDataProcessor implements ProcessorInterface
{
    /**
     * @var \Flurrybox\EnhancedPrivacy\Helper\Data
     */
    private $helperData;

    /**
     * @var \Magento\Sales\Model\Order\CreditmemoRepository
     */
    private $creditmemoRepository;

    /**
     * @param \Flurrybox\EnhancedPrivacy\Helper\Data $helperData
     */
    public function __construct(
        Data $helperData,
        CreditmemoRepository $creditmemoRepository
    ) {
        $this->helperData = $helperData;
        $this->creditmemoRepository = $creditmemoRepository;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(int $customerId, array $data): array
    {
        $creditmemoCollection = $this->creditmemoRepository->getList()->addFieldToFilter('customer_id', $customerId);

        return array_merge_recursive(
            $data,
            ['creditmemos' => $creditmemoCollection->toArray()]
        );
    }
}
