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
     * @var DateTime
     */
    private $performedAt;

    /**
     * @var string
     */
    private $state;

    /**
     * @var string
     */
    private $message;

    /**
     * @var array
     */
    private $result;

    public function __construct(
        DateTime $performedAt,
        string $state,
        string $message,
        array $result
    ) {
        $this->performedAt = $performedAt;
        $this->state = $state;
        $this->message = $message;
        $this->result = $result;
    }

    public function getPerformedAt(): DateTime
    {
        return $this->performedAt;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getResult(): array
    {
        return $this->result;
    }
}
