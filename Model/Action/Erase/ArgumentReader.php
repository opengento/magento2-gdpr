<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action\Erase;

use Opengento\Gdpr\Api\Data\ActionContextInterface;
use Opengento\Gdpr\Api\Data\EraseEntityInterface;

final class ArgumentReader
{
    public const ERASE_ENTITY = 'erase_entity';

    public static function getEntity(ActionContextInterface $actionContext): ?EraseEntityInterface
    {
        return $actionContext->getParameters()[self::ERASE_ENTITY] ?? null;
    }
}
