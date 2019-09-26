<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api\Data;

/**
 * @api
 */
interface ExportEntityInterface
{
    /**
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

    public function getExportId(): int;

    public function setExportId(int $exportId): ExportEntityInterface;

    public function getEntityId(): int;

    /**
     * Set the entity ID
     *
     * @param int $entityId
     * @return ExportEntityInterface
     * @todo force type in php7.4
     */
    public function setEntityId($entityId): ExportEntityInterface;

    public function getEntityType(): string;

    public function setEntityType(string $entityType): ExportEntityInterface;

    public function getFileName(): string;

    public function setFileName(string $filename): ExportEntityInterface;

    public function getFilePath(): ?string;

    public function setFilePath(?string $filePath): ExportEntityInterface;

    public function getCreatedAt(): string;

    public function setCreatedAt(string $createdAt): ExportEntityInterface;

    public function getExportedAt(): ?string;

    public function setExportedAt(string $exportedAt): ExportEntityInterface;

    public function getExpiredAt(): string;

    public function setExpiredAt(string $expiredAt): ExportEntityInterface;
}
