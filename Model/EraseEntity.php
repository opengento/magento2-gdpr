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

/**
 * Class EraseEntity
 */
class EraseEntity extends AbstractExtensibleModel implements EraseEntityInterface
{
    /**
     * @inheritdoc
     */
    protected $_eventPrefix = 'opengento_gdpr_erase_entity';

    /**
     * @inheritdoc
     */
    protected $_eventObject = 'erase_entity';

    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_init(EraseEntityResource::class);
    }

    /**
     * @inheritdoc
     */
    public function getEraseId(): int
    {
        return (int) $this->getId();
    }

    /**
     * @inheritdoc
     */
    public function setEraseId(int $eraseId): EraseEntityInterface
    {
        return $this->setId($eraseId);
    }

    /**
     * @inheritdoc
     */
    public function getEntityId(): int
    {
        return (int) $this->_getData(self::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId): EraseEntityInterface
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @inheritdoc
     */
    public function getEntityType(): string
    {
        return (string) $this->_getData(self::ENTITY_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setEntityType(string $entityType): EraseEntityInterface
    {
        return $this->setData(self::ENTITY_TYPE, $entityType);
    }

    /**
     * @inheritdoc
     */
    public function getScheduledAt(): string
    {
        return (string) $this->_getData(self::SCHEDULED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setScheduledAt(string $scheduledAt): EraseEntityInterface
    {
        return $this->setData(self::SCHEDULED_AT, $scheduledAt);
    }

    /**
     * @inheritdoc
     */
    public function getState(): string
    {
        return (string) $this->_getData(self::STATE);
    }

    /**
     * @inheritdoc
     */
    public function setState(string $state): EraseEntityInterface
    {
        return $this->setData(self::STATE, $state);
    }

    /**
     * @inheritdoc
     */
    public function getStatus(): string
    {
        return (string) $this->_getData(self::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus(string $status): EraseEntityInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritdoc
     */
    public function getMessage(): ?string
    {
        return $this->_getData(self::MESSAGE) === null ? null : (string) $this->_getData(self::MESSAGE);
    }

    /**
     * @inheritdoc
     */
    public function setMessage(?string $message): EraseEntityInterface
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * @inheritdoc
     */
    public function getErasedAt(): string
    {
        return (string) $this->_getData(self::ERASED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setErasedAt(string $erasedAt): EraseEntityInterface
    {
        return $this->setData(self::ERASED_AT, $erasedAt);
    }
}
