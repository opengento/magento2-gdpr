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
interface EraseEntityCheckerInterface
{
    public function exists(int $entityId, string $entityType): bool;

    public function canCreate(int $entityId, string $entityType): bool;

    public function canCancel(int $entityId, string $entityType): bool;

    public function canProcess(int $entityId, string $entityType): bool;
}
