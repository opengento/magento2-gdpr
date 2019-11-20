<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action\PerformedBy;

use Opengento\Gdpr\Model\Action\PerformedByInterface;

final class Console implements PerformedByInterface
{
    private const PERFORMED_BY = 'console';

    public function get(): string
    {
        return self::PERFORMED_BY;
    }
}
