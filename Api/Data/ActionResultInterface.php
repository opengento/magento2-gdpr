<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api\Data;

interface ActionResultInterface
{
    public function getPerformedAt()/*todo : DateTime*/;

    public function getState(): string;

    public function getMessage(): string;

    public function getResult(): array;
}
