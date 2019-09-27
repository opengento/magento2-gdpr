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
use Opengento\Gdpr\Api\Data\EraseEntitySearchResultsInterface;
use Opengento\Gdpr\Api\Data\EraseEntitySearchResultsInterfaceFactory;
use Opengento\Gdpr\Api\EraseEntityRepositoryInterface;
use Opengento\Gdpr\Model\ResourceModel\EraseEntity as EraseEntityResource;
use Opengento\Gdpr\Model\ResourceModel\EraseEntity\Collection;
use Opengento\Gdpr\Model\ResourceModel\EraseEntity\CollectionFactory;

final class EraseEntityRepository implements EraseEntityRepositoryInterface
{
    /**
     * @var EraseEntityResource
     */
    private $eraseEntityResource;

    /**
     * @var EraseEntityInterfaceFactory
     */
    private $eraseEntityFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var EraseEntitySearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var EraseEntityInterface[]
     */
    private $instances = [];

    /**
     * @var EraseEntityInterface[]
     */
    private $instancesByEntity = [];

    public function __construct(
        EraseEntityResource $eraseEntityResource,
        EraseEntityInterfaceFactory $eraseEntityFactory,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        EraseEntitySearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->eraseEntityResource = $eraseEntityResource;
        $this->eraseEntityFactory = $eraseEntityFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    public function save(EraseEntityInterface $entity): EraseEntityInterface
    {
        try {
            $this->eraseEntityResource->save($entity);
            $this->register($entity);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(new Phrase('Could not save the entity.'), $e);
        }

        return $entity;
    }

    public function getById(int $entityId, bool $forceReload = false): EraseEntityInterface
    {
        if ($forceReload || !isset($this->instances[$entityId])) {
            /** @var EraseEntityInterface $entity */
            $entity = $this->eraseEntityFactory->create();
            $this->eraseEntityResource->load($entity, $entityId, EraseEntityInterface::ID);

            if (!$entity->getEraseId()) {
                throw new NoSuchEntityException(new Phrase('Entity with id "%1" does not exists.', [$entityId]));
            }

            $this->register($entity);
        }

        return $this->instances[$entityId];
    }

    public function getByEntity(int $entityId, string $entityType, bool $forceReload = false): EraseEntityInterface
    {
        if ($forceReload || !isset($this->instancesByEntity[$entityType . '_' . $entityId])) {
            /** @var EraseEntityInterface $entity */
            $entity = $this->eraseEntityFactory->create();
            $this->eraseEntityResource->load(
                $entity,
                [$entityId, $entityType],
                [EraseEntityInterface::ENTITY_ID, EraseEntityInterface::ENTITY_TYPE]
            );

            if (!$entity->getEraseId()) {
                throw new NoSuchEntityException(
                    new Phrase('Entity with id "%1" does not exist.', [$entityId])
                );
            }

            $this->register($entity);
        }

        return $this->instancesByEntity[$entityType . '_' . $entityId];
    }

    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var EraseEntitySearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    public function delete(EraseEntityInterface $entity): bool
    {
        try {
            $this->remove($entity);
            $this->eraseEntityResource->delete($entity);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                new Phrase('Could not delete entity with id "%1".', [$entity->getEraseId()]),
                $e
            );
        }

        return true;
    }

    private function register(EraseEntityInterface $entity): void
    {
        $this->instances[$entity->getEraseId()] = $entity;
        $this->instancesByEntity[$entity->getEntityType() . '_' . $entity->getEntityId()] = $entity;
    }

    private function remove(EraseEntityInterface $entity): void
    {
        unset(
            $this->instances[$entity->getEraseId()],
            $this->instancesByEntity[$entity->getEntityType() . '_' . $entity->getEntityId()]
        );
    }
}
