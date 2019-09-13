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

/**
 * Class ExportEntity
 */
class ExportEntity extends AbstractExtensibleModel implements ExportEntityInterface
{
    /**
     * @inheritdoc
     */
    protected $_eventPrefix = 'opengento_gdpr_export_entity';

    /**
     * @inheritdoc
     */
    protected $_eventObject = 'export_entity';

    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_init(EraseEntityResource::class);
    }

    /**
     * @inheritdoc
     */
    public function getExportId(): int
    {
        return (int) $this->getId();
    }

    /**
     * @inheritdoc
     */
    public function setExportId(int $exportId): ExportEntityInterface
    {
        return $this->setId($exportId);
    }

    /**
     * @inheritdoc
     */
    public function getEntityId(): int
    {
        return (int) $this->_getData(self::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId): ExportEntityInterface
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @inheritdoc
     */
    public function getEntityType(): string
    {
        return (string) $this->_getData(self::ENTITY_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setEntityType(string $entityType): ExportEntityInterface
    {
        return $this->setData(self::ENTITY_TYPE, $entityType);
    }

    /**
     * @inheritdoc
     */
    public function getFileName(): string
    {
        return (string) $this->_getData(self::FILE_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setFileName(string $filename): ExportEntityInterface
    {
        return $this->setData(self::FILE_NAME, $filename);
    }

    /**
     * @inheritdoc
     */
    public function getFilePath(): ?string
    {
        return $this->_getData(self::FILE_PATH) === null ? null : (string) $this->_getData(self::FILE_PATH);
    }

    /**
     * @inheritdoc
     */
    public function setFilePath(string $filePath): ExportEntityInterface
    {
        return $this->setData(self::FILE_PATH, $filePath);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt(): string
    {
        return (string) $this->_getData(self::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt(string $createdAt): ExportEntityInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritdoc
     */
    public function getExportedAt(): ?string
    {
        return $this->_getData(self::EXPORTED_AT) === null ? null : (string) $this->_getData(self::EXPORTED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setExportedAt(string $exportedAt): ExportEntityInterface
    {
        return $this->setData(self::EXPORTED_AT, $exportedAt);
    }

    /**
     * @inheritdoc
     */
    public function getExpiredAt(): string
    {
        return (string) $this->_getData(self::EXPIRED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setExpiredAt(string $expiredAt): ExportEntityInterface
    {
        return $this->setData(self::EXPIRED_AT, $expiredAt);
    }
}
