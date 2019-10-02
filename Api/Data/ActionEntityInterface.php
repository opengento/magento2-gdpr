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
interface ActionEntityInterface extends ExtensibleDataInterface
{
    /**
     * Constants for fields keys
     */
    public const ID = 'action_id';
    public const TYPE = 'type';
    public const SCHEDULED_AT = 'scheduled_at';
    public const PERFORMED_FROM = 'performed_from';
    public const PERFORMED_BY = 'performed_by';
    public const PERFORMED_AT = 'performed_at';
    public const STATE = 'state';
    public const MESSAGE = 'message';
    public const PARAMETERS = 'parameters';
    public const RESULT = 'result';

    /**
     * Constants for State values
     */
    public const STATE_PENDING = 'pending';
    public const STATE_PROCESSING = 'processing';
    public const STATE_SUCCEEDED = 'succeeded';
    public const STATE_FAILED = 'failed';
    public const STATE_CANCELED = 'canceled';

    public function getActionId(): int;

    public function setActionId(int $actionId): ActionEntityInterface;

    public function getType(): string;

    public function setType(string $type): ActionEntityInterface;

    public function getScheduledAt(): ?string;

    public function setScheduledAt(?string $scheduledAt): ActionEntityInterface;

    public function getPerformedFrom(): ?string;

    public function setPerformedFrom(?string $performedFrom): ActionEntityInterface;

    public function getPerformedBy(): ?string;

    public function setPerformedBy(?string $performedBy): ActionEntityInterface;

    public function getPerformedAt(): string;

    public function setPerformedAt(string $performedAt): ActionEntityInterface;

    public function getState(): string;

    public function setState(string $state): ActionEntityInterface;

    public function getMessage(): string;

    public function setMessage(string $message): ActionEntityInterface;

    public function getParameters(): array;

    public function setParameters(array $parameters): ActionEntityInterface;

    public function getResult(): array;

    public function setResult(array $result): ActionEntityInterface;
}
