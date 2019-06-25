<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface EraseEntityInterface
 * @api
 */
interface EraseEntityInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants for fields keys
     */
    public const ID = 'erase_id';
    public const ENTITY_ID = 'entity_id';
    public const ENTITY_TYPE = 'entity_type';
    public const SCHEDULED_AT = 'scheduled_at';
    public const STATE = 'state';
    public const STATUS = 'status';
    public const MESSAGE = 'message';
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
     * Retrieve the erase ID
     *
     * @return int
     */
    public function getEraseId(): int;

    /**
     * Set the erase ID
     *
     * @param int $eraseId
     * @return \Opengento\Gdpr\Api\Data\EraseEntityInterface
     */
    public function setEraseId(int $eraseId): EraseEntityInterface;

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
     * @return \Opengento\Gdpr\Api\Data\EraseEntityInterface
     * @todo force type in php7.4
     */
    public function setEntityId($entityId): EraseEntityInterface;

    /**
     * Retrieve the entity type
     *
     * @return string
     */
    public function getEntityType(): string;

    /**
     * Set the entity type
     *
     * @param string $entityType
     * @return \Opengento\Gdpr\Api\Data\EraseEntityInterface
     */
    public function setEntityType(string $entityType): EraseEntityInterface;

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
     * @return \Opengento\Gdpr\Api\Data\EraseEntityInterface
     */
    public function setScheduledAt(string $scheduledAt): EraseEntityInterface;

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
     * @return \Opengento\Gdpr\Api\Data\EraseEntityInterface
     */
    public function setState(string $state): EraseEntityInterface;

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
     * @return \Opengento\Gdpr\Api\Data\EraseEntityInterface
     */
    public function setStatus(string $status): EraseEntityInterface;

    /**
     * Retrieve the error message
     *
     * @return string|null
     */
    public function getMessage(): ?string;

    /**
     * Set the error message
     *
     * @param string|null $message
     * @return \Opengento\Gdpr\Api\Data\EraseEntityInterface
     */
    public function setMessage(?string $message): EraseEntityInterface;

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
     * @return \Opengento\Gdpr\Api\Data\EraseEntityInterface
     */
    public function setErasedAt(string $erasedAt): EraseEntityInterface;
}
