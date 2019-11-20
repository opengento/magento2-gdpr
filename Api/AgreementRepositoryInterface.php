<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Opengento\Gdpr\Api\Data\AgreementInterface;
use Opengento\Gdpr\Api\Data\AgreementSearchResultsInterface;

/**
 * @api
 */
interface AgreementRepositoryInterface
{
    /**
     * Save agreement
     *
     * @param AgreementInterface $agreement
     * @return AgreementInterface
     * @throws CouldNotSaveException
     */
    public function save(AgreementInterface $agreement): AgreementInterface;

    /**
     * Retrieve agreement by ID
     *
     * @param int $agreementId
     * @return AgreementInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $agreementId): AgreementInterface;

    /**
     * Retrieve agreement by identifier and scope
     *
     * @param string $identifier
     * @param int $storeId
     * @return AgreementInterface
     * @throws NoSuchEntityException
     */
    public function getByIdentifier(string $identifier, int $storeId): AgreementInterface;

    /**
     * Retrieve agreement list by search filter criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return AgreementSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): AgreementSearchResultsInterface;

    /**
     * Delete agreement
     *
     * @param AgreementInterface $agreement
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function delete(AgreementInterface $agreement): bool;
}
