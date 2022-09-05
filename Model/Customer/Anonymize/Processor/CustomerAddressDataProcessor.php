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
    private AnonymizerInterface $anonymizer;

    /**
     * @var AddressRepositoryInterface
     */
    private AddressRepositoryInterface $addressRepository;

    private SearchCriteriaBuilder $criteriaBuilder;

    public function __construct(
        AnonymizerInterface $anonymizer,
        AddressRepositoryInterface $addressRepository,
        SearchCriteriaBuilder $criteriaBuilder
    ) {
        $this->anonymizer = $anonymizer;
        $this->addressRepository = $addressRepository;
        $this->criteriaBuilder = $criteriaBuilder;
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function execute(int $customerId): bool
    {
        $this->criteriaBuilder->addFilter('parent_id', $customerId);
        $addressList = $this->addressRepository->getList($this->criteriaBuilder->create());

        foreach ($addressList->getItems() as $address) {
            $this->addressRepository->save($this->anonymizer->anonymize($address));
        }

        return true;
    }
}
