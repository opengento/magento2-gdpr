<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action;

use DateTime;
use Opengento\Gdpr\Api\Data\ActionContextInterface;

final class Context implements ActionContextInterface
{
    /**
     * @var string
     */
    private $performedBy;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var DateTime|null
     */
    private $scheduledAt;

    public function __construct(string $performedBy, array $parameters, ?DateTime $scheduledAt = null)
    {
        $this->performedBy = $performedBy;
        $this->parameters = $parameters;
        $this->scheduledAt = $scheduledAt;
    }

    public function getPerformedBy(): string
    {
        return $this->performedBy;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getScheduledAt(): ?DateTime
    {
        return $this->scheduledAt;
    }
}
