<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Processor;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\EntityManager\Hydrator;
use Opengento\Gdpr\Model\Config;
use Opengento\Gdpr\Service\Export\ProcessorInterface;

/**
 * Class CustomerAddressDataProcessor
 */
final class CustomerAddressDataProcessor implements ProcessorInterface
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
     * @var \Magento\Framework\EntityManager\Hydrator
     */
    private $hydrator;

    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\EntityManager\Hydrator $hydrator
     * @param \Opengento\Gdpr\Model\Config $config
     */
    public function __construct(
        AddressRepositoryInterface $addressRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Hydrator $hydrator,
        Config $config
    ) {
        $this->addressRepository = $addressRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->hydrator = $hydrator;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(int $customerId, array $data): array
    {
        $this->searchCriteriaBuilder->addFilter('parent_id', $customerId);
        $addressList = $this->addressRepository->getList($this->searchCriteriaBuilder->create());
        $data['customer_addresses'] = $this->generateArray($addressList);

        return $data;
    }

    /**
     * Collect the customer addresses data to export
     *
     * @param \Magento\Framework\Api\SearchResultsInterface $searchResults
     * @return array
     */
    private function generateArray(SearchResultsInterface $searchResults): array
    {
        $result = [];

        /** @var \Magento\Customer\Api\Data\AddressInterface $entity */
        foreach ($searchResults->getItems() as $entity) {
            $data = [];
            $entityData = $this->hydrator->extract($entity);

            foreach ($this->config->getExportCustomerAddressAttributes() as $attributeCode) {
                if (isset($entityData[$attributeCode])) {
                    $data[$attributeCode] = $entityData[$attributeCode];
                }
            }

            $result['customer_address_id_' . $entity->getId()] = $data;
        }

        return $result;
    }
}
