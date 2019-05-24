<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Opengento\Gdpr\Api\Data\EraseCustomerInterface;

/**
 * Interface EraseCustomerRepositoryInterface
 * @api
 */
interface EraseCustomerRepositoryInterface
{
    /**
     * Save erase customer scheduler
     *
     * @param \Opengento\Gdpr\Api\Data\EraseCustomerInterface $entity
     * @return \Opengento\Gdpr\Api\Data\EraseCustomerInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(EraseCustomerInterface $entity): EraseCustomerInterface;

    /**
     * Retrieve erase customer scheduler by ID
     *
     * @param int $entityId
     * @return \Opengento\Gdpr\Api\Data\EraseCustomerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $entityId): EraseCustomerInterface;

    /**
     * Retrieve erase customer scheduler by customer ID
     *
     * @param int $entityId
     * @return \Opengento\Gdpr\Api\Data\EraseCustomerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCustomerId(int $entityId): EraseCustomerInterface;

    /**
     * Retrieve erase customer schedulers list by search filter criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Opengento\Gdpr\Api\Data\EraseCustomerSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * Delete erase customer scheduler by ID
     *
     * @param int $entityId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $entityId): bool;

    /**
     * Delete erase customer scheduler
     *
     * @param \Opengento\Gdpr\Api\Data\EraseCustomerInterface $entity
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(EraseCustomerInterface $entity): bool;
}
