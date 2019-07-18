<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Opengento\Gdpr\Api\Data\EraseEntityInterface;

/**
 * Interface EraseEntityRepositoryInterface
 * @api
 */
interface EraseEntityRepositoryInterface
{
    /**
     * Save erase entity scheduler
     *
     * @param \Opengento\Gdpr\Api\Data\EraseEntityInterface $entity
     * @return \Opengento\Gdpr\Api\Data\EraseEntityInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(EraseEntityInterface $entity): EraseEntityInterface;

    /**
     * Retrieve erase entity scheduler by ID
     *
     * @param int $entityId
     * @return \Opengento\Gdpr\Api\Data\EraseEntityInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $entityId): EraseEntityInterface;

    /**
     * Retrieve erase entity scheduler by entity
     *
     * @param int $entityId
     * @param string $entityType
     * @return \Opengento\Gdpr\Api\Data\EraseEntityInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByEntity(int $entityId, string $entityType): EraseEntityInterface;

    /**
     * Retrieve erase entity schedulers list by search filter criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Opengento\Gdpr\Api\Data\EraseEntitySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * Delete erase entity scheduler
     *
     * @param \Opengento\Gdpr\Api\Data\EraseEntityInterface $entity
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(EraseEntityInterface $entity): bool;
}
