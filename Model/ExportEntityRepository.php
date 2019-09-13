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
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Api\Data\ExportEntityInterfaceFactory;
use Opengento\Gdpr\Api\Data\ExportEntitySearchResultsInterfaceFactory;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;
use Opengento\Gdpr\Model\ResourceModel\ExportEntity as ExportEntityResource;
use Opengento\Gdpr\Model\ResourceModel\ExportEntity\CollectionFactory;

/**
 * Class ExportEntityRepository
 */
final class ExportEntityRepository implements ExportEntityRepositoryInterface
{
    /**
     * @var \Opengento\Gdpr\Model\ResourceModel\ExportEntity
     */
    private $exportEntityResource;

    /**
     * @var \Opengento\Gdpr\Api\Data\ExportEntityInterfaceFactory
     */
    private $exportCustomerFactory;

    /**
     * @var \Opengento\Gdpr\Model\ResourceModel\ExportEntity\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var \Opengento\Gdpr\Api\Data\ExportEntitySearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var \Opengento\Gdpr\Api\Data\ExportEntityInterface[]
     */
    private $instances = [];

    /**
     * @var \Opengento\Gdpr\Api\Data\ExportEntityInterface[]
     */
    private $instancesByEntity = [];

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $fileSystem;

    /**
     * @param ExportEntityResource $exportEntityResource
     * @param ExportEntityInterfaceFactory $exportCustomerFactory
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param ExportEntitySearchResultsInterfaceFactory $searchResultsFactory
     * @param Filesystem $filesystem
     */
    public function __construct(
        ExportEntityResource $exportEntityResource,
        ExportEntityInterfaceFactory $exportCustomerFactory,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        ExportEntitySearchResultsInterfaceFactory $searchResultsFactory,
        Filesystem $filesystem
    ) {
        $this->fileSystem = $filesystem;
        $this->exportEntityResource = $exportEntityResource;
        $this->exportCustomerFactory = $exportCustomerFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @inheritdoc
     */
    public function save(ExportEntityInterface $entity): ExportEntityInterface
    {
        try {
            $this->exportEntityResource->save($entity);
            $this->register($entity);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(new Phrase('Could not save the entity.'), $e);
        }

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $entityId, bool $forceReload = false): ExportEntityInterface
    {
        if ($forceReload || !isset($this->instances[$entityId])) {
            /** @var \Opengento\Gdpr\Api\Data\ExportEntityInterface $entity */
            $entity = $this->exportCustomerFactory->create();
            $this->exportEntityResource->load($entity, $entityId, ExportEntityInterface::ID);

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
    public function getByEntity(int $entityId, string $entityType, bool $forceReload = false): ExportEntityInterface
    {
        if ($forceReload || !isset($this->instancesByEntity[$entityId])) {
            /** @var \Opengento\Gdpr\Api\Data\ExportEntityInterface $entity */
            $entity = $this->exportCustomerFactory->create();
            $this->exportEntityResource->load(
                $entity,
                [$entityId, $entityType],
                [ExportEntityInterface::ENTITY_ID, ExportEntityInterface::ENTITY_TYPE]
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
        /** @var \Opengento\Gdpr\Model\ResourceModel\ExportEntity\Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var \Opengento\Gdpr\Api\Data\ExportEntitySearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function delete(ExportEntityInterface $entity): bool
    {
        try {
            $this->removeFile($entity);
            $this->remove($entity);
            $this->exportEntityResource->delete($entity);
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
     * @param \Opengento\Gdpr\Api\Data\ExportEntityInterface $entity
     * @return void
     */
    private function register(ExportEntityInterface $entity): void
    {
        $this->instances[$entity->getEntityId()] = $entity;
        $this->instancesByEntity[$entity->getEntityType() . '_' . $entity->getEntityId()] = $entity;
    }

    /**
     * Remove the entity from the registry
     *
     * @param \Opengento\Gdpr\Api\Data\ExportEntityInterface $entity
     * @return void
     */
    private function remove(ExportEntityInterface $entity): void
    {
        unset(
            $this->instances[$entity->getEntityId()],
            $this->instancesByEntity[$entity->getEntityType() . '_' . $entity->getEntityId()]
        );
    }

    private function removeFile(ExportEntityInterface $entity): void
    {
        $this->fileSystem->getDirectoryWrite(DirectoryList::TMP)->delete($entity->getFileName());
    }
}
