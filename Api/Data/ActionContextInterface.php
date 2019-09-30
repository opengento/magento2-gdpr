<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api\Data;

use DateTime;

interface ActionContextInterface
{
    public function getPerformedBy(): string;

    public function getParameters(): array;

    public function getScheduledAt(): ?DateTime;
}
