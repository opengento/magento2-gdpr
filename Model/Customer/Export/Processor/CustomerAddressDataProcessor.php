<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Export\Processor;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Opengento\Gdpr\Model\Entity\DataCollectorInterface;
use Opengento\Gdpr\Service\Export\Processor\AbstractDataProcessor;

/**
 * Class CustomerAddressDataProcessor
 */
final class CustomerAddressDataProcessor extends AbstractDataProcessor
{
    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Opengento\Gdpr\Model\Entity\DataCollectorInterface $dataCollector
     */
    public function __construct(
        AddressRepositoryInterface $addressRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataCollectorInterface $dataCollector
    ) {
        $this->addressRepository = $addressRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($dataCollector);
    }

    /**
     * @inheritdoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(int $customerId, array $data): array
    {
        $this->searchCriteriaBuilder->addFilter('parent_id', $customerId);
        $addressList = $this->addressRepository->getList($this->searchCriteriaBuilder->create());

        /** @var \Magento\Customer\Api\Data\AddressInterface $entity */
        foreach ($addressList->getItems() as $entity) {
            $data['customer_addresses']['customer_address_id_' . $entity->getId()] = $this->collectData($entity);
        }

        return $data;
    }
}
