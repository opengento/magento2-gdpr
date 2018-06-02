<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Flurrybox\EnhancedPrivacy\Service\Export;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Flurrybox\EnhancedPrivacy\Helper\Data;

/**
 * Class CustomerAddressDataProcessor
 */
class CustomerAddressDataProcessor implements ProcessorInterface
{
    /**
     * @var \Flurrybox\EnhancedPrivacy\Helper\Data
     */
    private $helperData;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param \Flurrybox\EnhancedPrivacy\Helper\Data $helperData
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Data $helperData,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->helperData = $helperData;
        $this->customerRepository = $customerRepository;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(int $customerId, array $data): array
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->customerRepository->getById($customerId);
        $addressCollection = $customer->getAddressesCollection();

        return array_merge_recursive(
            $data,
            ['customer_addresses' => $addressCollection->toArray($this->helperData->{/*@todo getAddressesAttributesCodesFromConfig*/})]
        );
    }
}
