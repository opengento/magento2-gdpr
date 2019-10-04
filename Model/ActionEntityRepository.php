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
use Opengento\Gdpr\Api\ActionEntityRepositoryInterface;
use Opengento\Gdpr\Api\Data\ActionEntityInterface;
use Opengento\Gdpr\Api\Data\ActionEntityInterfaceFactory;
use Opengento\Gdpr\Api\Data\ActionEntitySearchResultsInterface;
use Opengento\Gdpr\Api\Data\ActionEntitySearchResultsInterfaceFactory;
use Opengento\Gdpr\Model\ResourceModel\ActionEntity as ActionEntityResource;
use Opengento\Gdpr\Model\ResourceModel\ActionEntity\Collection;
use Opengento\Gdpr\Model\ResourceModel\ActionEntity\CollectionFactory;

final class ActionEntityRepository implements ActionEntityRepositoryInterface
{
    /**
     * @var ActionEntityResource
     */
    private $actionEntityResource;

    /**
     * @var ActionEntityInterfaceFactory
     */
    private $actionCustomerFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var ActionEntitySearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var ActionEntityInterface[]
     */
    private $instances = [];

    public function __construct(
        ActionEntityResource $actionEntityResource,
        ActionEntityInterfaceFactory $actionCustomerFactory,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        ActionEntitySearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->actionEntityResource = $actionEntityResource;
        $this->actionCustomerFactory = $actionCustomerFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    public function save(ActionEntityInterface $entity): ActionEntityInterface
    {
        try {
            $this->actionEntityResource->save($entity);
            $this->instances[$entity->getActionId()] = $entity;
        } catch (Exception $e) {
            throw new CouldNotSaveException(new Phrase('Could not save the entity.'), $e);
        }

        return $entity;
    }

    public function getById(int $entityId, bool $forceReload = false): ActionEntityInterface
    {
        if ($forceReload || !isset($this->instances[$entityId])) {
            /** @var ActionEntityInterface $entity */
            $entity = $this->actionCustomerFactory->create();
            $this->actionEntityResource->load($entity, $entityId, ActionEntityInterface::ID);

            if (!$entity->getActionId()) {
                throw new NoSuchEntityException(new Phrase('Entity with id "%1" does not exists.', [$entityId]));
            }

            $this->instances[$entityId] = $entity;
        }

        return $this->instances[$entityId];
    }

    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var ActionEntitySearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    public function delete(ActionEntityInterface $entity): bool
    {
        try {
            unset($this->instances[$entity->getActionId()]);
            $this->actionEntityResource->delete($entity);
        } catch (Exception $e) {
            throw new CouldNotDeleteException(
                new Phrase('Could not delete entity with id "%1".', [$entity->getActionId()]),
                $e
            );
        }

        return true;
    }
}
