<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action;

use Opengento\Gdpr\Api\Data\ActionContextInterface;

final class Context implements ActionContextInterface
{
    /**
     * @var string
     */
    private $performedFrom;

    /**
     * @var string
     */
    private $performedBy;

    /**
     * @var array
     */
    private $parameters;

    public function __construct(
        string $performedFrom,
        string $performedBy,
        array $parameters
    ) {
        $this->performedFrom = $performedFrom;
        $this->performedBy = $performedBy;
        $this->parameters = $parameters;
    }

    public function getPerformedFrom(): string
    {
        return $this->performedFrom;
    }

    public function getPerformedBy(): string
    {
        return $this->performedBy;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
