<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action;

use Opengento\Gdpr\Api\Data\ActionEntityInterface;

final class ArgumentReader
{
    public const ENTITY_TYPE = 'entity_type';
    public const ENTITY_ID = 'entity_id';

    public static function getEntityType(ActionEntityInterface $actionEntity): ?string
    {
        return $actionEntity->getParameters()[self::ENTITY_TYPE] ?? null;
    }

    public static function getEntityId(ActionEntityInterface $actionEntity): ?string
    {
        return $actionEntity->getParameters()[self::ENTITY_ID] ?? null;
    }
}
