<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api;

/**
 * Interface ExportEntityCheckerInterface
 * @api
 */
interface ExportEntityCheckerInterface
{
    /**
     * Check if an export entity already exists for this entity
     *
     * @param int $entityId
     * @param string $entityType
     * @return bool
     */
    public function exists(int $entityId, string $entityType): bool;

    /**
     * Check if the document is exported and ready
     *
     * @param int $entityId
     * @param string $entityType
     * @return bool
     */
    public function isExported(int $entityId, string $entityType): bool;
}
