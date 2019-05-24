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
     * Export all data related to a given entity ID to the file
     *
     * @param int $customerId
     * @param string $fileName
     * @return string
     */
    public function exportToFile(int $customerId, string $fileName): string;
}
