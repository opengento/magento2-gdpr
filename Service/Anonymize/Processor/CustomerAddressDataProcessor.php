<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Processor;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Opengento\Gdpr\Service\Anonymize\AnonymizeTool;
use Opengento\Gdpr\Service\Anonymize\ProcessorInterface;

/**
 * Class CustomerAddressDataProcessor
 */
class CustomerAddressDataProcessor implements ProcessorInterface
{
    /**
     * @var \Opengento\Gdpr\Service\Anonymize\AnonymizeTool
     */
    private $anonymizeTool;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    private $customerAddressRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param \Opengento\Gdpr\Service\Anonymize\AnonymizeTool $anonymizeTool
     * @param \Magento\Customer\Api\AddressRepositoryInterface $customerAddressRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        AnonymizeTool $anonymizeTool,
        AddressRepositoryInterface $customerAddressRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->anonymizeTool = $anonymizeTool;
        $this->customerAddressRepository = $customerAddressRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(int $customerId): bool
    {
        $this->searchCriteriaBuilder->addFilter(AddressInterface::CUSTOMER_ID, $customerId);
        $addressList = $this->customerAddressRepository->getList($this->searchCriteriaBuilder->create());
        $anonymousValue = $this->anonymizeTool->anonymousValue();

        foreach ($addressList->getItems() as $address) {
            $address->setFirstname($anonymousValue);
            $address->setMiddlename($anonymousValue);
            $address->setLastname($anonymousValue);
            $address->setStreet([$anonymousValue]);
            $address->setCity($anonymousValue);
            $address->setTelephone($anonymousValue);
            $address->setPostcode($anonymousValue);

            $this->customerAddressRepository->save($address);
        }

        return true;
    }
}
