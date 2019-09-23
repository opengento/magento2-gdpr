<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Anonymize\Processor;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;

final class CustomerAddressDataProcessor implements ProcessorInterface
{
    /**
     * @var AnonymizerInterface
     */
    private $anonymizer;

    /**
     * @var AddressRepositoryInterface
     */
    private $customerAddressRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

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
     * @throws LocalizedException
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
