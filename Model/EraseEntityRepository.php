<?php
/**
 * Copyright © OpenGento, All rights reserved.
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
use Opengento\Gdpr\Api\Data\EraseEntityInterface;
use Opengento\Gdpr\Api\Data\EraseEntityInterfaceFactory;
use Opengento\Gdpr\Api\Data\EraseEntitySearchResultsInterfaceFactory;
use Opengento\Gdpr\Api\EraseEntityRepositoryInterface;
use Opengento\Gdpr\Model\ResourceModel\EraseEntity as EraseCustomerResource;
use Opengento\Gdpr\Model\ResourceModel\EraseEntity\CollectionFactory;

/**
 * Class EraseEntityRepository
 */
final class EraseEntityRepository implements EraseEntityRepositoryInterface
{
    /**
     * @var \Opengento\Gdpr\Model\ResourceModel\EraseEntity
     */
    private $eraseCustomerResource;

    /**
     * @var \Opengento\Gdpr\Api\Data\EraseEntityInterfaceFactory
     */
    private $eraseCustomerFactory;

    /**
     * @var \Opengento\Gdpr\Model\ResourceModel\EraseEntity\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var \Opengento\Gdpr\Api\Data\EraseEntitySearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var \Opengento\Gdpr\Api\Data\EraseEntityInterface[]
     */
    private $instances = [];

    /**
     * @var \Opengento\Gdpr\Api\Data\EraseEntityInterface[]
     */
    private $instancesByEntity = [];

    /**
     * @param \Opengento\Gdpr\Model\ResourceModel\EraseEntity $eraseCustomerResource
     * @param \Opengento\Gdpr\Api\Data\EraseEntityInterfaceFactory $eraseCustomerFactory
     * @param \Opengento\Gdpr\Model\ResourceModel\EraseEntity\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
     * @param \Opengento\Gdpr\Api\Data\EraseEntitySearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        EraseCustomerResource $eraseCustomerResource,
        EraseEntityInterfaceFactory $eraseCustomerFactory,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        EraseEntitySearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->eraseCustomerResource = $eraseCustomerResource;
        $this->eraseCustomerFactory = $eraseCustomerFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @inheritdoc
     */
    public function save(EraseEntityInterface $entity): EraseEntityInterface
    {
        try {
            $this->eraseCustomerResource->save($entity);
            $this->register($entity);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(new Phrase('Could not save the entity.'), $e);
        }

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $entityId, bool $forceReload = false): EraseEntityInterface
    {
        if ($forceReload || !isset($this->instances[$entityId])) {
            /** @var \Opengento\Gdpr\Api\Data\EraseEntityInterface $entity */
            $entity = $this->eraseCustomerFactory->create();
            $this->eraseCustomerResource->load($entity, $entityId, EraseEntityInterface::ID);

            if (!$entity->getEntityId()) {
                throw new NoSuchEntityException(new Phrase('Entity with id "%1" does not exists.', [$entityId]));
            }

            $this->register($entity);
        }

        return $this->instances[$entityId];
    }

    /**
     * @inheritdoc
     */
    public function getByEntity(int $entityId, string $entityType, bool $forceReload = false): EraseEntityInterface
    {
        if ($forceReload || !isset($this->instancesByCustomer[$entityId])) {
            /** @var \Opengento\Gdpr\Api\Data\EraseEntityInterface $entity */
            $entity = $this->eraseCustomerFactory->create();
            $this->eraseCustomerResource->load(
                $entity,
                [$entityId, $entityType],
                [EraseEntityInterface::ENTITY_ID, EraseEntityInterface::ENTITY_TYPE]
            );

            if (!$entity->getEntityId()) {
                throw new NoSuchEntityException(
                    new Phrase('Entity with customer id "%1" does not exist.', [$entityId])
                );
            }

            $this->register($entity);
        }

        return $this->instancesByEntity[$entityType . '_' . $entityId];
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        /** @var \Opengento\Gdpr\Model\ResourceModel\EraseEntity\Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var \Opengento\Gdpr\Api\Data\EraseEntitySearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function delete(EraseEntityInterface $entity): bool
    {
        try {
            $this->remove($entity);
            $this->eraseCustomerResource->delete($entity);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                new Phrase('Could not delete entity with id "%1".', [$entity->getEntityId()]),
                $e
            );
        }

        return true;
    }

    /**
     * Register the entity into the registry
     *
     * @param \Opengento\Gdpr\Api\Data\EraseEntityInterface $entity
     * @return void
     */
    private function register(EraseEntityInterface $entity): void
    {
        $this->instances[$entity->getEntityId()] = $entity;
        $this->instancesByEntity[$entity->getEntityType() . '_' . $entity->getEntityId()] = $entity;
    }

    /**
     * Remove the entity from the registry
     *
     * @param \Opengento\Gdpr\Api\Data\EraseEntityInterface $entity
     * @return void
     */
    private function remove(EraseEntityInterface $entity): void
    {
        unset(
            $this->instances[$entity->getEntityId()],
            $this->instancesByEntity[$entity->getEntityType() . '_' . $entity->getEntityId()]
        );
    }
}
