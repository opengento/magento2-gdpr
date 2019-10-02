<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use Opengento\Gdpr\Api\Data\ActionEntityInterface;
use Opengento\Gdpr\Model\ResourceModel\ActionEntity as ActionEntityResource;

class ActionEntity extends AbstractExtensibleModel implements ActionEntityInterface
{
    protected function _construct(): void
    {
        $this->_eventPrefix = 'opengento_gdpr_action_entity';
        $this->_eventObject = 'action_entity';
        $this->_init(ActionEntityResource::class);
    }

    public function getActionId(): int
    {
        return (int) $this->_getData(self::ID);
    }

    public function setActionId(int $actionId): ActionEntityInterface
    {
        return $this->setData(self::ID, $actionId);
    }

    public function getType(): string
    {
       return (string) $this->_getData(self::TYPE);
    }

    public function setType(string $type): ActionEntityInterface
    {
        return $this->setData(self::TYPE, $type);
    }

    public function getScheduledAt(): ?string
    {
        return $this->_getData(self::SCHEDULED_AT) === null ? null : (string) $this->_getData(self::SCHEDULED_AT);
    }

    public function setScheduledAt(?string $scheduledAt): ActionEntityInterface
    {
        return $this->setData(self::SCHEDULED_AT, $scheduledAt);
    }

    public function getPerformedFrom(): ?string
    {
        return $this->_getData(self::PERFORMED_FROM) === null ? null : (string) $this->_getData(self::PERFORMED_FROM);
    }

    public function setPerformedFrom(?string $performedFrom): ActionEntityInterface
    {
        return $this->setData(self::PERFORMED_FROM, $performedFrom);
    }

    public function getPerformedBy(): ?string
    {
        return $this->_getData(self::PERFORMED_BY) === null ? null : (string) $this->_getData(self::PERFORMED_BY);
    }

    public function setPerformedBy(?string $performedBy): ActionEntityInterface
    {
        return $this->setData(self::PERFORMED_BY, $performedBy);
    }

    public function getPerformedAt(): string
    {
        return (string) $this->_getData(self::PERFORMED_AT);
    }

    public function setPerformedAt(string $performedAt): ActionEntityInterface
    {
        return $this->setData(self::PERFORMED_AT, $performedAt);
    }

    public function getState(): string
    {
        return (string) $this->_getData(self::STATE);
    }

    public function setState(string $state): ActionEntityInterface
    {
        return $this->setData(self::STATE, $state);
    }

    public function getMessage(): string
    {
        return (string) $this->_getData(self::MESSAGE);
    }

    public function setMessage(string $message): ActionEntityInterface
    {
        return $this->setData(self::MESSAGE, $message);
    }

    public function getParameters(): array
    {
        return (array) $this->_getData(self::PARAMETERS);
    }

    public function setParameters(array $parameters): ActionEntityInterface
    {
        return $this->setData(self::PARAMETERS, $parameters);
    }

    public function getResult(): array
    {
        return $this->_getData(self::RESULT);
    }

    public function setResult(array $result): ActionEntityInterface
    {
        return $this->setData(self::RESULT, $result);
    }
}
