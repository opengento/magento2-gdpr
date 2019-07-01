<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Opengento\Gdpr\Api\Data\ExportEntityInterface;

/**
 * Class ExportEntity
 */
final class ExportEntity implements ExportEntityInterface
{
    /**
     * @var int
     */
    private $entityId;

    /**
     * @var string
     */
    private $entityType;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @param int|null $entityId
     * @param string|null $entityType
     * @param string|null $fileName
     */
    public function __construct(
        ?int $entityId = null,
        ?string $entityType = null,
        ?string $fileName = null
    ) {
        $this->entityId = $entityId;
        $this->entityType = $entityType;
        $this->fileName = $fileName;
    }

    /**
     * @inheritdoc
     */
    public function getEntityId(): int
    {
        return $this->entityId;
    }

    /**
     * @inheritdoc
     */
    public function getEntityType(): string
    {
        return $this->entityType;
    }

    /**
     * @inheritdoc
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }
}
