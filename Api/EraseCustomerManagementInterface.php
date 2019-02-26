<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

namespace Opengento\Gdpr\Api;

use Opengento\Gdpr\Api\Data\EraseCustomerInterface;

/**
 * Interface EraseCustomerManagementInterface
 * @api
 */
interface EraseCustomerManagementInterface
{
    /**
     * Initialize and save a new erase customer scheduler for a customer ID
     *
     * @param int $customerId
     * @return \Opengento\Gdpr\Api\Data\EraseCustomerInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function create(int $customerId): EraseCustomerInterface;

    /**
     * Cancel and remove erase customer scheduler for a customer ID
     *
     * @param int $customerId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function cancel(int $customerId): bool;

    /**
     * Run and process the erase customer scheduler command
     *
     * @param \Opengento\Gdpr\Api\Data\EraseCustomerInterface $entity
     * @return \Opengento\Gdpr\Api\Data\EraseCustomerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function process(EraseCustomerInterface $entity): EraseCustomerInterface;

    /**
     * Check if an erase scheduler already exists for this customer ID
     *
     * @param int $customerId
     * @return bool
     */
    public function exists(int $customerId): bool;

    /**
     * Check if an erase scheduler can be canceled
     *
     * @param \Opengento\Gdpr\Api\Data\EraseCustomerInterface $entity
     * @return bool
     */
    public function canBeCanceled(EraseCustomerInterface $entity): bool;

    /**
     * Check if an erase scheduler can be processed
     *
     * @param \Opengento\Gdpr\Api\Data\EraseCustomerInterface $entity
     * @return bool
     */
    public function canBeProcessed(EraseCustomerInterface $entity): bool;
}
