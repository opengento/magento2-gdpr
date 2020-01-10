<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Opengento\Gdpr\Api\Data\ActionEntityInterface;
use Opengento\Gdpr\Api\Data\ActionEntitySearchResultsInterface;

/**
 * @api
 */
interface ActionEntityRepositoryInterface
{
    /**
     * Save action entity
     *
     * @param ActionEntityInterface $actionEntity
     * @return ActionEntityInterface
     * @throws CouldNotSaveException
     */
    public function save(ActionEntityInterface $actionEntity): ActionEntityInterface;

    /**
     * Retrieve action entity by ID
     *
     * @param int $actionId
     * @return ActionEntityInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $actionId): ActionEntityInterface;

    /**
     * Retrieve action entity list by search filter criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return ActionEntitySearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * Delete action entity
     *
     * @param ActionEntityInterface $actionEntity
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function delete(ActionEntityInterface $actionEntity): bool;
}
