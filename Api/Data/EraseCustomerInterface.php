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
    const ID = 'entity_id';
    const CUSTOMER_ID = 'customer_id';
    const SCHEDULED_AT = 'scheduled_at';
    const STATE = 'state';
    const STATUS = 'status';
    const ERASED_AT = 'erased_at';
    /**#@-*/

    /**#@+
     * Constants for State values
     */
    const STATE_PENDING = 'pending';
    const STATE_PROCESSING = 'processing';
    const STATE_COMPLETE = 'complete';
    /**#@-*/

    /**#@+
     * Constants for Status values
     */
    const STATUS_READY = 'ready';
    const STATUS_RUNNING = 'running';
    const STATUS_FAILED = 'failed';
    const STATUS_SUCCEED = 'succeed';
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
    public function getErasedAt();

    /**
     * Set the erased at
     *
     * @param string $erasedAt
     * @return \Opengento\Gdpr\Api\Data\EraseCustomerInterface
     */
    public function setErasedAt(string $erasedAt): EraseCustomerInterface;
}
