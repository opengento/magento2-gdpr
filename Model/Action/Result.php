<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action;

use DateTime;
use Opengento\Gdpr\Api\Data\ActionResultInterface;

final class Result implements ActionResultInterface
{
    /**
     * @var string
     */
    private $state;

    /**
     * @var DateTime
     */
    private $performedAt;

    /**
     * @var array
     */
    private $result;

    public function __construct(string $state, DateTime $performedAt, array $result)
    {
        $this->state = $state;
        $this->performedAt = $performedAt;
        $this->result = $result;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getPerformedAt(): DateTime
    {
        return $this->performedAt;
    }

    public function getResult(): array
    {
        return $this->result;
    }
}
