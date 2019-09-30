<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use DateTime;
use Magento\Framework\Stdlib\DateTime as DateTimeFormat;
use Opengento\Gdpr\Api\Data\ActionEntityInterface;
use Opengento\Gdpr\Api\Data\ActionEntityInterfaceFactory;

/**
 * @api
 */
final class ActionEntityBuilder
{
    /**
     * @var ActionEntityInterfaceFactory
     */
    private $actionEntityFactory;

    /**
     * @var array
     */
    private $data;

    public function __construct(
        ActionEntityInterfaceFactory $actionEntityFactory
    ) {
        $this->actionEntityFactory = $actionEntityFactory;
        $this->data = [];
    }

    public function setType(string $type): ActionEntityBuilder
    {
        $this->data[ActionEntityInterface::TYPE] = $type;

        return $this;
    }

    public function setScheduledAt(DateTime $dateTime): ActionEntityBuilder
    {
        $this->data[ActionEntityInterface::SCHEDULED_AT] = $dateTime->format(DateTimeFormat::DATETIME_PHP_FORMAT);

        return $this;
    }

    public function setPerformedBy(string $performedBy): ActionEntityBuilder
    {
        $this->data[ActionEntityInterface::PERFORMED_BY] = $performedBy;

        return $this;
    }

    public function setParameters(array $parameters): ActionEntityBuilder
    {
        $this->data[ActionEntityInterface::PARAMETERS] = $parameters;

        return $this;
    }

    public function addParameter(string $key, $value): ActionEntityBuilder
    {
        $this->data[ActionEntityInterface::PARAMETERS][$key] = $value;

        return $this;
    }

    public function create(): ActionEntityInterface
    {
        /** @var ActionEntityInterface $actionEntity */
        $actionEntity = $this->actionEntityFactory->create(['data' => $this->data]);
        $this->data = [];

        return  $actionEntity;
    }
}
