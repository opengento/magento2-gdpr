<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

namespace Opengento\Gdpr\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface EraseCustomerInterface
 * @api
 */
interface EraseCustomerInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants for fields keys
     */
    public const ID = 'entity_id';
    public const CUSTOMER_ID = 'customer_id';
    public const SCHEDULED_AT = 'scheduled_at';
    public const STATE = 'state';
    public const STATUS = 'status';
    public const ERASED_AT = 'erased_at';
    /**#@-*/

    /**#@+
     * Constants for State values
     */
    public const STATE_PENDING = 'pending';
    public const STATE_PROCESSING = 'processing';
    public const STATE_COMPLETE = 'complete';
    /**#@-*/

    /**#@+
     * Constants for Status values
     */
    public const STATUS_READY = 'ready';
    public const STATUS_RUNNING = 'running';
    public const STATUS_FAILED = 'failed';
    public const STATUS_SUCCEED = 'succeed';
    /**#@-*/

    /**
     * Retrieve the entity ID
     *
     * @return int
     */
    public function getEntityId(): int;

    /**
     * Set the entity ID
     *
     * @param int $entityId
     * @return \Opengento\Gdpr\Api\Data\EraseCustomerInterface
     */
    public function setEntityId($entityId): EraseCustomerInterface;

    /**
     * Retrieve the customer ID
     *
     * @return int
     */
    public function getCustomerId(): int;

    /**
     * Set the customer ID
     *
     * @param int $customerId
     * @return \Opengento\Gdpr\Api\Data\EraseCustomerInterface
     */
    public function setCustomerId(int $customerId): EraseCustomerInterface;

    /**
     * Retrieve the scheduled at
     *
     * @return string
     */
    public function getScheduledAt(): string;

    /**
     * Set the schedule at
     *
     * @param string $scheduledAt
     * @return \Opengento\Gdpr\Api\Data\EraseCustomerInterface
     */
    public function setScheduledAt(string $scheduledAt): EraseCustomerInterface;

    /**
     * Retrieve the state
     *
     * @return string
     */
    public function getState(): string;

    /**
     * Set the state
     *
     * @param string $state
     * @return \Opengento\Gdpr\Api\Data\EraseCustomerInterface
     */
    public function setState(string $state): EraseCustomerInterface;

    /**
     * Retrieve the status
     *
     * @return string
     */
    public function getStatus(): string;

    /**
     * Set the status
     *
     * @param string $status
     * @return \Opengento\Gdpr\Api\Data\EraseCustomerInterface
     */
    public function setStatus(string $status): EraseCustomerInterface;

    /**
     * Retrieve the erased at if it exists
     *
     * @return string|null
     */
    public function getErasedAt(): ?string;

    /**
     * Set the erased at
     *
     * @param string $erasedAt
     * @return \Opengento\Gdpr\Api\Data\EraseCustomerInterface
     */
    public function setErasedAt(string $erasedAt): EraseCustomerInterface;
}
