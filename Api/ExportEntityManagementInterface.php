<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api;

use Opengento\Gdpr\Api\Data\ExportEntityInterface;

/**
 * Interface ExportEntityManagementInterface
 * @api
 */
interface ExportEntityManagementInterface
{
    /**
     * Export all data related to a given entity to the file
     *
     * @param \Opengento\Gdpr\Api\Data\ExportEntityInterface $exportEntity
     * @return string
     */
    public function export(ExportEntityInterface $exportEntity): string;
}
