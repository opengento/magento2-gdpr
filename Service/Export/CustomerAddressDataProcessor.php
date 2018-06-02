<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Flurrybox\EnhancedPrivacy\Service\Export;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Flurrybox\EnhancedPrivacy\Model\Config;

/**
 * Class CustomerAddressDataProcessor
 */
class CustomerAddressDataProcessor implements ProcessorInterface
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Flurrybox\EnhancedPrivacy\Model\Config
     */
    private $config;

    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Flurrybox\EnhancedPrivacy\Model\Config
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        Config $config
    ) {
        $this->customerRepository = $customerRepository;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(string $customerEmail, array $data): array
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->customerRepository->get($customerEmail);
        $addressCollection = $customer->getAddressesCollection();

        return array_merge_recursive(
            $data,
            ['customer_addresses' => $addressCollection->toArray($this->config->getAnonymizeCustomerAddressAttributes())]
        );
    }
}
