<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action\Export;

use Opengento\Gdpr\Api\Data\ActionEntityInterface;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;

final class ArgumentReader
{
    public const EXPORT_ENTITY = 'export_entity';
    public const EXPORT_FILE_NAME = 'export_file_name';

    public static function getEntity(ActionEntityInterface $actionEntity): ?ExportEntityInterface
    {
        return $actionEntity->getParameters()[self::EXPORT_ENTITY] ?? null;
    }

    public static function getFileName(ActionEntityInterface $actionEntity): ?string
    {
        return $actionEntity->getParameters()[self::EXPORT_FILE_NAME] ?? null;
    }
}
