<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action\Export;

use InvalidArgumentException;
use Opengento\Gdpr\Api\Data\ActionEntityInterface;
use Opengento\Gdpr\Model\Action\ArgumentReader;
use Opengento\Gdpr\Model\Action\ProcessorInterface;
use Opengento\Gdpr\Model\Export\ExportEntityData;

final class CreateOrExportProcessor implements ProcessorInterface
{
    /**
     * @var ExportEntityData
     */
    private $exportEntityData;

    public function __construct(
        ExportEntityData $exportEntityData
    ) {
        $this->exportEntityData = $exportEntityData;
    }

    public function execute(ActionEntityInterface $actionEntity): array
    {
        $entityId = ArgumentReader::getEntityId($actionEntity);
        $entityType = ArgumentReader::getEntityType($actionEntity);

        if ($entityId === null || $entityType === null) {
            throw new InvalidArgumentException('Arguments "entity_id" and "entity_type" are required.');
        }

        return ['export_file_path' => $this->exportEntityData->export($entityId, $entityType)];
    }
}
