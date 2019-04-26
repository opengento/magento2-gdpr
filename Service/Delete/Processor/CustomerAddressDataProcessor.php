<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Delete\Processor;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Opengento\Gdpr\Service\Delete\ProcessorInterface;

/**
 * Class CustomerAddressDataProcessor
 */
final class CustomerAddressDataProcessor implements ProcessorInterface
{
    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    private $customerAddressRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param \Magento\Customer\Api\AddressRepositoryInterface $customerAddressRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        AddressRepositoryInterface $customerAddressRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->customerAddressRepository = $customerAddressRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritdoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(int $customerId): bool
    {
        $this->searchCriteriaBuilder->addFilter('parent_id', $customerId);
        $addressList = $this->customerAddressRepository->getList($this->searchCriteriaBuilder->create());

        foreach ($addressList->getItems() as $address) {
            $this->customerAddressRepository->delete($address);
        }

        return true;
    }
}
