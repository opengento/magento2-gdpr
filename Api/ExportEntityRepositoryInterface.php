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
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Api\Data\ExportEntitySearchResultsInterface;

/**
 * @api
 */
interface ExportEntityRepositoryInterface
{
    /**
     * Save export entity
     *
     * @param ExportEntityInterface $entity
     * @return ExportEntityInterface
     * @throws CouldNotSaveException
     */
    public function save(ExportEntityInterface $entity): ExportEntityInterface;

    /**
     * Retrieve export entity by ID
     *
     * @param int $entityId
     * @return ExportEntityInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $entityId): ExportEntityInterface;

    /**
     * Retrieve export by entity
     *
     * @param int $entityId
     * @param string $entityType
     * @return ExportEntityInterface
     * @throws NoSuchEntityException
     */
    public function getByEntity(int $entityId, string $entityType): ExportEntityInterface;

    /**
     * Retrieve export entity list by search filter criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return ExportEntitySearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * Delete export entity
     *
     * @param ExportEntityInterface $entity
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function delete(ExportEntityInterface $entity): bool;
}
