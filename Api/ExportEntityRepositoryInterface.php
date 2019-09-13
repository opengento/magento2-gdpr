<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;

/**
 * Interface ExportEntityRepositoryInterface
 * @api
 */
interface ExportEntityRepositoryInterface
{
    /**
     * Save export entity
     *
     * @param \Opengento\Gdpr\Api\Data\ExportEntityInterface $entity
     * @return \Opengento\Gdpr\Api\Data\ExportEntityInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(ExportEntityInterface $entity): ExportEntityInterface;

    /**
     * Retrieve export entity by ID
     *
     * @param int $entityId
     * @return \Opengento\Gdpr\Api\Data\ExportEntityInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $entityId): ExportEntityInterface;

    /**
     * Retrieve export by entity
     *
     * @param int $entityId
     * @param string $entityType
     * @return \Opengento\Gdpr\Api\Data\ExportEntityInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByEntity(int $entityId, string $entityType): ExportEntityInterface;

    /**
     * Retrieve export entity list by search filter criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Opengento\Gdpr\Api\Data\ExportEntitySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * Delete export entity
     *
     * @param \Opengento\Gdpr\Api\Data\ExportEntityInterface $entity
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(ExportEntityInterface $entity): bool;
}
