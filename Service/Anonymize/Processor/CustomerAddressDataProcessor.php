<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Processor;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;
use Opengento\Gdpr\Service\Anonymize\ProcessorInterface;

/**
 * Class CustomerAddressDataProcessor
 */
final class CustomerAddressDataProcessor implements ProcessorInterface
{
    /**
     * @var \Opengento\Gdpr\Service\Anonymize\AnonymizerInterface
     */
    private $anonymizer;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    private $customerAddressRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param \Opengento\Gdpr\Service\Anonymize\AnonymizerInterface $anonymizer
     * @param \Magento\Customer\Api\AddressRepositoryInterface $customerAddressRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        AnonymizerInterface $anonymizer,
        AddressRepositoryInterface $customerAddressRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->anonymizer = $anonymizer;
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
            $this->customerAddressRepository->save($this->anonymizer->anonymize($address));
        }

        return true;
    }
}
