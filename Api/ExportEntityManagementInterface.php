<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;

/**
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
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function create(int $entityId, string $entityType, ?string $fileName = null): ExportEntityInterface;

    /**
     * Export all data related to a given entity to the file
     *
     * @param ExportEntityInterface $exportEntity
     * @return string
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function export(ExportEntityInterface $exportEntity): string;
}
