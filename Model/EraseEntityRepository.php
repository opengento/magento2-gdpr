<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Exception;
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

class EraseEntityRepository implements EraseEntityRepositoryInterface
{
    /**
     * @var EraseEntityResource
     */
    private EraseEntityResource $eraseEntityResource;

    private EraseEntityInterfaceFactory $eraseEntityFactory;

    private CollectionFactory $collectionFactory;

    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @var EraseEntitySearchResultsInterfaceFactory
     */
    private EraseEntitySearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * @var EraseEntityInterface[]
     */
    private array $instances = [];

    /**
     * @var EraseEntityInterface[]
     */
    private array $instancesByEntity = [];

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

    public function save(EraseEntityInterface $eraseEntity): EraseEntityInterface
    {
        try {
            $this->eraseEntityResource->save($eraseEntity);
            $this->register($eraseEntity);
        } catch (Exception $e) {
            throw new CouldNotSaveException(new Phrase('Could not save the entity.'), $e);
        }

        return $eraseEntity;
    }

    public function getById(int $eraseId): EraseEntityInterface
    {
        if (!isset($this->instances[$eraseId])) {
            /** @var EraseEntityInterface $eraseEntity */
            $eraseEntity = $this->eraseEntityFactory->create();
            $this->eraseEntityResource->load($eraseEntity, $eraseId, EraseEntityInterface::ID);

            if (!$eraseEntity->getEraseId()) {
                throw NoSuchEntityException::singleField(EraseEntityInterface::ID, $eraseId);
            }

            $this->register($eraseEntity);
        }

        return $this->instances[$eraseId];
    }

    public function getByEntity(int $entityId, string $entityType): EraseEntityInterface
    {
        $key = $entityType . '_' . $entityId;

        if (!isset($this->instancesByEntity[$key])) {
            /** @var EraseEntityInterface $eraseEntity */
            $eraseEntity = $this->eraseEntityFactory->create();
            $this->eraseEntityResource->load(
                $eraseEntity,
                [$entityId, $entityType],
                [EraseEntityInterface::ENTITY_ID, EraseEntityInterface::ENTITY_TYPE]
            );

            if (!$eraseEntity->getEraseId()) {
                throw NoSuchEntityException::doubleField(
                    EraseEntityInterface::ENTITY_ID,
                    $entityId,
                    EraseEntityInterface::ENTITY_TYPE,
                    $entityType
                );
            }

            $this->register($eraseEntity);
        }

        return $this->instancesByEntity[$key];
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

    public function delete(EraseEntityInterface $eraseEntity): bool
    {
        try {
            $this->remove($eraseEntity);
            $this->eraseEntityResource->delete($eraseEntity);
        } catch (Exception $e) {
            throw new CouldNotDeleteException(
                new Phrase('Could not delete entity with id "%1".', [$eraseEntity->getEraseId()]),
                $e
            );
        }

        return true;
    }

    private function register(EraseEntityInterface $eraseEntity): void
    {
        $this->instances[$eraseEntity->getEraseId()] = $eraseEntity;
        $this->instancesByEntity[$eraseEntity->getEntityType() . '_' . $eraseEntity->getEntityId()] = $eraseEntity;
    }

    private function remove(EraseEntityInterface $eraseEntity): void
    {
        unset(
            $this->instances[$eraseEntity->getEraseId()],
            $this->instancesByEntity[$eraseEntity->getEntityType() . '_' . $eraseEntity->getEntityId()]
        );
    }
}
