<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Model\ResourceModel\ExportEntity as EraseEntityResource;

class ExportEntity extends AbstractExtensibleModel implements ExportEntityInterface
{
    protected function _construct(): void
    {
        $this->_eventPrefix = 'opengento_gdpr_export_entity';
        $this->_eventObject = 'export_entity';
        $this->_init(EraseEntityResource::class);
    }

    public function getExportId(): int
    {
        return (int) $this->getId();
    }

    public function setExportId(int $exportId): ExportEntityInterface
    {
        return $this->setId($exportId);
    }

    public function getEntityId(): int
    {
        return (int) $this->_getData(self::ENTITY_ID);
    }

    public function setEntityId($entityId): ExportEntityInterface
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    public function getEntityType(): string
    {
        return (string) $this->_getData(self::ENTITY_TYPE);
    }

    public function setEntityType(string $entityType): ExportEntityInterface
    {
        return $this->setData(self::ENTITY_TYPE, $entityType);
    }

    public function getFileName(): string
    {
        return (string) $this->_getData(self::FILE_NAME);
    }

    public function setFileName(string $filename): ExportEntityInterface
    {
        return $this->setData(self::FILE_NAME, $filename);
    }

    public function getFilePath(): ?string
    {
        return $this->_getData(self::FILE_PATH) === null ? null : (string) $this->_getData(self::FILE_PATH);
    }

    public function setFilePath(string $filePath): ExportEntityInterface
    {
        return $this->setData(self::FILE_PATH, $filePath);
    }

    public function getCreatedAt(): string
    {
        return (string) $this->_getData(self::CREATED_AT);
    }

    public function setCreatedAt(string $createdAt): ExportEntityInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    public function getExportedAt(): ?string
    {
        return $this->_getData(self::EXPORTED_AT) === null ? null : (string) $this->_getData(self::EXPORTED_AT);
    }

    public function setExportedAt(string $exportedAt): ExportEntityInterface
    {
        return $this->setData(self::EXPORTED_AT, $exportedAt);
    }

    public function getExpiredAt(): string
    {
        return (string) $this->_getData(self::EXPIRED_AT);
    }

    public function setExpiredAt(string $expiredAt): ExportEntityInterface
    {
        return $this->setData(self::EXPIRED_AT, $expiredAt);
    }
}
