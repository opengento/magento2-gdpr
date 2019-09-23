<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api;

/**
 * @api
 */
interface ExportEntityCheckerInterface
{
    public function exists(int $entityId, string $entityType): bool;

    public function isExported(int $entityId, string $entityType): bool;
}
