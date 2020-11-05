<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Export\Processor;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Opengento\Gdpr\Model\Entity\DataCollectorInterface;
use Opengento\Gdpr\Service\Export\Processor\AbstractDataProcessor;

final class CustomerAddressDataProcessor extends AbstractDataProcessor
{
    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $criteriaBuilder;

    public function __construct(
        AddressRepositoryInterface $addressRepository,
        SearchCriteriaBuilder $criteriaBuilder,
        DataCollectorInterface $dataCollector
    ) {
        $this->addressRepository = $addressRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        parent::__construct($dataCollector);
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function execute(int $customerId, array $data): array
    {
        $this->criteriaBuilder->addFilter('parent_id', $customerId);
        $addressList = $this->addressRepository->getList($this->criteriaBuilder->create());

        /** @var AddressInterface $entity */
        foreach ($addressList->getItems() as $entity) {
            $data['customer_addresses']['customer_address_id_' . $entity->getId()] = $this->collectData($entity);
        }

        return $data;
    }
}
