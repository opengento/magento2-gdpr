<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action;

use DateTime;
use Opengento\Gdpr\Api\Data\ActionContextInterface;
use Opengento\Gdpr\Api\Data\ActionContextInterfaceFactory;

/**
 * @api
 */
final class ContextBuilder
{
    /**
     * @var ActionContextInterfaceFactory
     */
    private $actionContextFactory;

    /**
     * @var array
     */
    private $data;

    public function __construct(
        ActionContextInterfaceFactory $actionContextFactory
    ) {
        $this->actionContextFactory = $actionContextFactory;
        $this->data = [];
    }

    public function setPerformedBy(string $performedBy): ContextBuilder
    {
        $this->data['performedBy'] = $performedBy;

        return $this;
    }

    public function setParameters(array $parameters): ContextBuilder
    {
        $this->data['parameters'] = $parameters;

        return $this;
    }

    public function setScheduledAt(?DateTime $scheduledAt): ContextBuilder
    {
        $this->data['scheduledAt'] = $scheduledAt;

        return $this;
    }

    public function create(): ActionContextInterface
    {
        /** @var ActionContextInterface $context */
        $context = $this->actionContextFactory->create($this->data);
        $this->data = [];

        return $context;
    }
}
