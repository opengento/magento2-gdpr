<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\Data\EraseCustomerInterface;
use Opengento\Gdpr\Api\Data\EraseCustomerInterfaceFactory;
use Opengento\Gdpr\Api\Data\EraseCustomerSearchResultsInterfaceFactory;
use Opengento\Gdpr\Api\EraseCustomerRepositoryInterface;
use Opengento\Gdpr\Model\ResourceModel\EraseCustomer as EraseCustomerResource;
use Opengento\Gdpr\Model\ResourceModel\EraseCustomer\CollectionFactory;

/**
 * Class EraseCustomerRepository
 */
class EraseCustomerRepository implements EraseCustomerRepositoryInterface
{
    /**#@+
     * Constants for register keys
     */
    const REGISTER_KEY = EraseCustomerInterface::class;
    const REGISTER_KEY_MIRROR = EraseCustomerInterface::class . '_customer';
    /**#@-*/

    /**
     * @var \Opengento\Gdpr\Model\ResourceModel\EraseCustomer
     */
    private $eraseCustomerResource;

    /**
     * @var \Opengento\Gdpr\Api\Data\EraseCustomerInterfaceFactory
     */
    private $eraseCustomerFactory;

    /**
     * @var \Opengento\Gdpr\Model\ResourceModel\EraseCustomer\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Opengento\Gdpr\Api\Data\EraseCustomerSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var \Opengento\Gdpr\Api\Data\EraseCustomerInterface[]
     */
    private $instances = [];

    /**
     * @var \Opengento\Gdpr\Api\Data\EraseCustomerInterface[]
     */
    private $instancesByCustomer = [];

    /**
     * @param \Opengento\Gdpr\Model\ResourceModel\EraseCustomer $eraseCustomerResource
     * @param \Opengento\Gdpr\Api\Data\EraseCustomerInterfaceFactory $eraseCustomerFactory
     * @param \Opengento\Gdpr\Model\ResourceModel\EraseCustomer\CollectionFactory $collectionFactory
     * @param \Opengento\Gdpr\Api\Data\EraseCustomerSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        EraseCustomerResource $eraseCustomerResource,
        EraseCustomerInterfaceFactory $eraseCustomerFactory,
        CollectionFactory $collectionFactory,
        EraseCustomerSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->eraseCustomerResource = $eraseCustomerResource;
        $this->eraseCustomerFactory = $eraseCustomerFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(EraseCustomerInterface $entity): EraseCustomerInterface
    {
        try {
            $this->eraseCustomerResource->save($entity);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(new Phrase('Could not save the entity.'), $e);
        }

        return $this->getById($entity->getEntityId(), true);
    }

    /**
     * {@inheritdoc}
     */
    public function getById(int $entityId, bool $forceReload = false): EraseCustomerInterface
    {
        if (!isset($this->instances[$entityId]) || $forceReload) {
            /** @var \Opengento\Gdpr\Api\Data\EraseCustomerInterface $entity */
            $entity = $this->eraseCustomerFactory->create();
            $this->eraseCustomerResource->load($entity, $entityId, EraseCustomerInterface::ID);

            if (!$entity->getEntityId()) {
                throw new NoSuchEntityException(new Phrase('Entity with id "%1" does not exists.', [$entityId]));
            }

            $this->instances[$entityId] = $entity;
            $this->instancesByCustomer[$entity->getCustomerId()] = $entity;
        }

        return $this->instances[$entityId];
    }

    /**
     * {@inheritdoc}
     */
    public function getByCustomerId(int $entityId, bool $forceReload = false): EraseCustomerInterface
    {
        if (!isset($this->instancesByCustomer[$entityId]) || $forceReload) {
            /** @var \Opengento\Gdpr\Api\Data\EraseCustomerInterface $entity */
            $entity = $this->eraseCustomerFactory->create();
            $this->eraseCustomerResource->load($entity, $entityId, EraseCustomerInterface::CUSTOMER_ID);

            if (!$entity->getEntityId()) {
                throw new NoSuchEntityException(
                    new Phrase('Entity with customer id "%1" does not exist.', [$entityId])
                );
            }

            $this->instances[$entity->getEntityId()] = $entity;
            $this->instancesByCustomer[$entityId] = $entity;
        }

        return $this->instancesByCustomer[$entityId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        /** @var \Opengento\Gdpr\Model\ResourceModel\EraseCustomer\Collection $collection */
        $collection = $this->collectionFactory->create();

        /** @var \Opengento\Gdpr\Api\Data\EraseCustomerSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];

            foreach ($filterGroup->getFilters() as $filter) {
                $fields[] = $filter->getField();
                $conditions[] = [$filter->getConditionType() => $filter->getValue()];
            }

            $collection->addFieldToFilter($fields, $conditions);
        }

        if ($searchCriteria->getSortOrders()) {
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $collection->addOrder($sortOrder->getField(), $sortOrder->getDirection());
            }
        }

        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->count());

        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(EraseCustomerInterface $entity): bool
    {
        try {
            if (isset($this->instances[$entity->getEntityId()])) {
                unset($this->instances[$entity->getEntityId()]);
            }
            if (isset($this->instancesByCustomer[$entity->getCustomerId()])) {
                unset($this->instancesByCustomer[$entity->getCustomerId()]);
            }
            $this->eraseCustomerResource->delete($entity);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                new Phrase('Could not delete entity with id "%1".', [$entity->getEntityId()]), $e
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById(int $entityId): bool
    {
        return $this->delete($this->getById($entityId));
    }
}
