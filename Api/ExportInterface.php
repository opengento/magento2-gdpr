<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api;

/**
 * Interface ExportInterface
 * @api
 */
interface ExportInterface
{
    /**
     * Export all data related to a given entity to the file
     *
     * @param int $entityId
     * @param string $entityType
     * @param string $fileName
     * @return string
     */
    public function exportToFile(int $entityId, string $entityType, string $fileName): string;
}
