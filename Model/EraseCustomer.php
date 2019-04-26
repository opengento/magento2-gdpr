<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use Opengento\Gdpr\Api\Data\EraseCustomerInterface;
use Opengento\Gdpr\Model\ResourceModel\EraseCustomer as EraseCustomerResource;

/**
 * Erase Customer Model
 */
final class EraseCustomer extends AbstractExtensibleModel implements EraseCustomerInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_init(EraseCustomerResource::class);
    }

    /**
     * @inheritdoc
     */
    public function getEntityId(): int
    {
        return (int) parent::getEntityId();
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId): EraseCustomerInterface
    {
        parent::setEntityId((int) $entityId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustomerId(): int
    {
        return (int) $this->_getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerId(int $customerId): EraseCustomerInterface
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
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
    public function setScheduledAt(string $scheduledAt): EraseCustomerInterface
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
    public function setState(string $state): EraseCustomerInterface
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
    public function setStatus(string $status): EraseCustomerInterface
    {
        return $this->setData(self::STATUS, $status);
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
    public function setErasedAt(string $erasedAt): EraseCustomerInterface
    {
        return $this->setData(self::ERASED_AT, $erasedAt);
    }
}
