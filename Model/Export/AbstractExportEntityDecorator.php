<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Export;

use Opengento\Gdpr\Api\Data\ExportEntityInterface;

/**
 * Class AbstractExportEntityDecorator
 */
abstract class AbstractExportEntityDecorator implements ExportEntityInterface
{
    /**
     * @var \Opengento\Gdpr\Api\Data\ExportEntityInterface
     */
    protected $exportEntity;

    /**
     * @param \Opengento\Gdpr\Api\Data\ExportEntityInterface $exportEntity
     */
    final public function __construct(
        ExportEntityInterface $exportEntity
    ) {
        $this->exportEntity = $exportEntity;
    }

    /**
     * @inheritdoc
     */
    public function getEntityId(): int
    {
        return $this->exportEntity->getEntityId();
    }

    /**
     * @inheritdoc
     */
    public function getEntityType(): string
    {
        return $this->exportEntity->getEntityType();
    }

    /**
     * @inheritdoc
     */
    public function getFileName(): string
    {
        return $this->exportEntity->getFileName();
    }
}
