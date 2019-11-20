<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use Opengento\Gdpr\Api\Data\EraseEntityInterface;
use Opengento\Gdpr\Model\ResourceModel\EraseEntity as EraseEntityResource;

class EraseEntity extends AbstractExtensibleModel implements EraseEntityInterface
{
    protected function _construct(): void
    {
        $this->_eventPrefix = 'opengento_gdpr_erase_entity';
        $this->_eventObject = 'erase_entity';
        $this->_init(EraseEntityResource::class);
    }

    public function getEraseId(): int
    {
        return (int) $this->getId();
    }

    public function setEraseId(int $eraseId): EraseEntityInterface
    {
        return $this->setId($eraseId);
    }

    public function getEntityId(): int
    {
        return (int) $this->_getData(self::ENTITY_ID);
    }

    public function setEntityId($entityId): EraseEntityInterface
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    public function getEntityType(): string
    {
        return (string) $this->_getData(self::ENTITY_TYPE);
    }

    public function setEntityType(string $entityType): EraseEntityInterface
    {
        return $this->setData(self::ENTITY_TYPE, $entityType);
    }

    public function getScheduledAt(): string
    {
        return (string) $this->_getData(self::SCHEDULED_AT);
    }

    public function setScheduledAt(string $scheduledAt): EraseEntityInterface
    {
        return $this->setData(self::SCHEDULED_AT, $scheduledAt);
    }

    public function getState(): string
    {
        return (string) $this->_getData(self::STATE);
    }

    public function setState(string $state): EraseEntityInterface
    {
        return $this->setData(self::STATE, $state);
    }

    public function getStatus(): string
    {
        return (string) $this->_getData(self::STATUS);
    }

    public function setStatus(string $status): EraseEntityInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    public function getMessage(): ?string
    {
        return $this->_getData(self::MESSAGE) === null ? null : (string) $this->_getData(self::MESSAGE);
    }

    public function setMessage(?string $message): EraseEntityInterface
    {
        return $this->setData(self::MESSAGE, $message);
    }

    public function getErasedAt(): string
    {
        return (string) $this->_getData(self::ERASED_AT);
    }

    public function setErasedAt(string $erasedAt): EraseEntityInterface
    {
        return $this->setData(self::ERASED_AT, $erasedAt);
    }
}
