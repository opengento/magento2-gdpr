<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api;

use Magento\Framework\Exception\AlreadyExistsException;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;

/**
 * Interface ExportEntityManagementInterface
 * @api
 */
interface ExportEntityManagementInterface
{
    /**
     * Initialize and save a new export for an entity
     *
     * @param int $entityId
     * @param string $entityType
     * @param null|string $fileName [optional]
     * @return ExportEntityInterface
     * @throws AlreadyExistsException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create(int $entityId, string $entityType, ?string $fileName = null): ExportEntityInterface;

    /**
     * Export all data related to a given entity to the file
     *
     * @param \Opengento\Gdpr\Api\Data\ExportEntityInterface $exportEntity
     * @return string
     */
    public function export(ExportEntityInterface $exportEntity): string;
}
