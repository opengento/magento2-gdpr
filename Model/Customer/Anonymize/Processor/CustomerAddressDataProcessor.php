<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Anonymize\Processor;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;

class CustomerAddressDataProcessor implements ProcessorInterface
{
    public function __construct(
        private AnonymizerInterface $anonymizer,
        private AddressRepositoryInterface $addressRepository,
        private SearchCriteriaBuilder $criteriaBuilder
    ) {}

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function execute(int $customerId): bool
    {
        $this->criteriaBuilder->addFilter('parent_id', $customerId);
        $addressList = $this->addressRepository->getList($this->criteriaBuilder->create());

        foreach ($addressList->getItems() as $address) {
            $address = $this->anonymizer->anonymize($address);
            if ($address instanceof DataObject) {
                $address->setData('should_ignore_validation', true);
            }
            $this->addressRepository->save($address);
        }

        return true;
    }
}
