<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api\Data;

/**
 * Interface ExportEntityInterface
 * @api
 */
interface ExportEntityInterface
{
    /**#@+
     * Constants for fields keys
     */
    public const ID = 'export_id';
    public const ENTITY_ID = 'entity_id';
    public const ENTITY_TYPE = 'entity_type';
    public const FILE_NAME = 'file_name';
    public const FILE_PATH = 'file_path';
    public const CREATED_AT = 'created_at';
    public const EXPORTED_AT = 'exported_at';
    public const EXPIRED_AT = 'expired_at';
    /**#@-*/

    /**
     * Retrieve the export ID
     *
     * @return int
     */
    public function getExportId(): int;

    /**
     * Set the export ID
     *
     * @param int $exportId
     * @return ExportEntityInterface
     */
    public function setExportId(int $exportId): ExportEntityInterface;

    /**
     * Retrieve the entity ID to export
     *
     * @return int
     */
    public function getEntityId(): int;

    /**
     * Set the entity ID to export
     *
     * @param int $entityId
     * @return ExportEntityInterface
     */
    public function setEntityId($entityId): ExportEntityInterface;

    /**
     * Retrieve the entity type to export
     *
     * @return string
     */
    public function getEntityType(): string;

    /**
     * Set the entity type to export
     *
     * @param string $entityType
     * @return ExportEntityInterface
     * @todo force type in php7.4
     */
    public function setEntityType(string $entityType): ExportEntityInterface;

    /**
     * Retrieve the file name to export the data to
     *
     * @return string
     */
    public function getFileName(): string;

    /**
     * Set the file name to export the data to
     *
     * @param string $filename
     * @return ExportEntityInterface
     */
    public function setFileName(string $filename): ExportEntityInterface;

    /**
     * Retrieve the export file absolute path
     *
     * @return string|null
     */
    public function getFilePath(): ?string;

    /**
     * Set the export file absolute path
     *
     * @param string $filePath
     * @return ExportEntityInterface
     */
    public function setFilePath(string $filePath): ExportEntityInterface;

    /**
     * Retrieve the created at date of the export
     *
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * Set the created at date of the export
     *
     * @param string $createdAt
     * @return ExportEntityInterface
     */
    public function setCreatedAt(string $createdAt): ExportEntityInterface;

    /**
     * Retrieve the last exported at date
     *
     * @return string|null
     */
    public function getExportedAt(): ?string;

    /**
     * Set the last exported at date
     *
     * @param string $exportedAt
     * @return ExportEntityInterface
     */
    public function setExportedAt(string $exportedAt): ExportEntityInterface;

    /**
     * Retrieve the date expiration of the export
     *
     * @return string
     */
    public function getExpiredAt(): string;

    /**
     * Set the expiration date of the export
     *
     * @param string $expiredAt
     * @return ExportEntityInterface
     */
    public function setExpiredAt(string $expiredAt): ExportEntityInterface;
}
