<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * @api
 */
interface EraseEntityInterface extends ExtensibleDataInterface
{
    /**
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

    /**
     * Constants for State values
     */
    public const STATE_PENDING = 'pending';
    public const STATE_PROCESSING = 'processing';
    public const STATE_COMPLETE = 'complete';

    /**
     * Constants for Status values
     */
    public const STATUS_READY = 'ready';
    public const STATUS_RUNNING = 'running';
    public const STATUS_FAILED = 'failed';
    public const STATUS_SUCCEED = 'succeed';

    public function getEraseId(): int;

    public function setEraseId(int $eraseId): EraseEntityInterface;

    public function getEntityId(): int;

    public function setEntityId(int $entityId): EraseEntityInterface;

    public function getEntityType(): string;

    public function setEntityType(string $entityType): EraseEntityInterface;

    public function getScheduledAt(): string;

    public function setScheduledAt(string $scheduledAt): EraseEntityInterface;

    public function getState(): string;

    public function setState(string $state): EraseEntityInterface;

    public function getStatus(): string;

    public function setStatus(string $status): EraseEntityInterface;

    public function getMessage(): ?string;

    public function setMessage(?string $message): EraseEntityInterface;

    public function getErasedAt(): ?string;

    public function setErasedAt(string $erasedAt): EraseEntityInterface;
}
