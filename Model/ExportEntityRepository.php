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
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Api\Data\ExportEntityInterfaceFactory;
use Opengento\Gdpr\Api\Data\ExportEntitySearchResultsInterface;
use Opengento\Gdpr\Api\Data\ExportEntitySearchResultsInterfaceFactory;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;
use Opengento\Gdpr\Model\ResourceModel\ExportEntity as ExportEntityResource;
use Opengento\Gdpr\Model\ResourceModel\ExportEntity\Collection;
use Opengento\Gdpr\Model\ResourceModel\ExportEntity\CollectionFactory;

final class ExportEntityRepository implements ExportEntityRepositoryInterface
{
    /**
     * @var ExportEntityResource
     */
    private $exportEntityResource;

    /**
     * @var ExportEntityInterfaceFactory
     */
    private $exportEntityFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var ExportEntitySearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var ExportEntityInterface[]
     */
    private $instances = [];

    /**
     * @var ExportEntityInterface[]
     */
    private $instancesByEntity = [];

    public function __construct(
        ExportEntityResource $exportEntityResource,
        ExportEntityInterfaceFactory $exportEntityFactory,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        ExportEntitySearchResultsInterfaceFactory $searchResultsFactory,
        Filesystem $filesystem
    ) {
        $this->exportEntityResource = $exportEntityResource;
        $this->exportEntityFactory = $exportEntityFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->fileSystem = $filesystem;
    }

    public function save(ExportEntityInterface $exportEntity): ExportEntityInterface
    {
        try {
            $this->exportEntityResource->save($exportEntity);
            $this->register($exportEntity);
        } catch (Exception $e) {
            throw new CouldNotSaveException(new Phrase('Could not save the entity.'), $e);
        }

        return $exportEntity;
    }

    public function getById(int $exportId): ExportEntityInterface
    {
        if (!isset($this->instances[$exportId])) {
            /** @var ExportEntityInterface $exportEntity */
            $exportEntity = $this->exportEntityFactory->create();
            $this->exportEntityResource->load($exportEntity, $exportId, ExportEntityInterface::ID);

            if (!$exportEntity->getExportId()) {
                throw NoSuchEntityException::singleField(ExportEntityInterface::ID, $exportId);
            }

            $this->register($exportEntity);
        }

        return $this->instances[$exportId];
    }

    public function getByEntity(int $entityId, string $entityType): ExportEntityInterface
    {
        if (!isset($this->instancesByEntity[$entityType . '_' . $entityId])) {
            /** @var ExportEntityInterface $exportEntity */
            $exportEntity = $this->exportEntityFactory->create();
            $this->exportEntityResource->load(
                $exportEntity,
                [$entityId, $entityType],
                [ExportEntityInterface::ENTITY_ID, ExportEntityInterface::ENTITY_TYPE]
            );

            if (!$exportEntity->getExportId()) {
                throw NoSuchEntityException::doubleField(
                    ExportEntityInterface::ENTITY_ID,
                    $entityId,
                    ExportEntityInterface::ENTITY_TYPE,
                    $entityType
                );
            }

            $this->register($exportEntity);
        }

        return $this->instancesByEntity[$entityType . '_' . $entityId];
    }

    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var ExportEntitySearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    public function delete(ExportEntityInterface $exportEntity): bool
    {
        try {
            $this->fileSystem->getDirectoryWrite(DirectoryList::TMP)->delete($exportEntity->getFilePath());
            $this->remove($exportEntity);
            $this->exportEntityResource->delete($exportEntity);
        } catch (Exception $e) {
            throw new CouldNotDeleteException(
                new Phrase('Could not delete entity with id "%1".', [$exportEntity->getExportId()]),
                $e
            );
        }

        return true;
    }

    private function register(ExportEntityInterface $exportEntity): void
    {
        $this->instances[$exportEntity->getExportId()] = $exportEntity;
        $this->instancesByEntity[$exportEntity->getEntityType() . '_' . $exportEntity->getEntityId()] = $exportEntity;
    }

    private function remove(ExportEntityInterface $exportEntity): void
    {
        unset(
            $this->instances[$exportEntity->getExportId()],
            $this->instancesByEntity[$exportEntity->getEntityType() . '_' . $exportEntity->getEntityId()]
        );
    }
}
