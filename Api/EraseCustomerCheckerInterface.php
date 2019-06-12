<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api;

/**
 * Interface EraseCustomerCheckerInterface
 * @api
 */
interface EraseCustomerCheckerInterface
{
    /**
     * Check if an erase scheduler already exists for this customer ID
     *
     * @param int $customerId
     * @return bool
     */
    public function exists(int $customerId): bool;

    /**
     * Check if a customer erase scheduler can be created for this customer ID
     *
     * @param int $customerId
     * @return bool
     */
    public function canCreate(int $customerId): bool;

    /**
     * Check if a customer erase scheduler can be canceled
     *
     * @param int $customerId
     * @return bool
     */
    public function canCancel(int $customerId): bool;

    /**
     * Check if a customer erase scheduler can be processed
     *
     * @param int $customerId
     * @return bool
     */
    public function canProcess(int $customerId): bool;
}
