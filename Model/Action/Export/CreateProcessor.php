<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action\Export;

use InvalidArgumentException;
use Opengento\Gdpr\Api\Data\ActionEntityInterface;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Model\Action\ArgumentReader;
use Opengento\Gdpr\Model\Action\Export\ArgumentReader as ExportArgumentReader;
use Opengento\Gdpr\Model\Action\ProcessorInterface;

final class CreateProcessor implements ProcessorInterface
{
    /**
     * @var ExportEntityManagementInterface
     */
    private $exportEntityManagement;

    public function __construct(
        ExportEntityManagementInterface $exportEntityManagement
    ) {
        $this->exportEntityManagement = $exportEntityManagement;
    }

    public function execute(ActionEntityInterface $actionEntity): array
    {
        $entityId = ArgumentReader::getEntityId($actionEntity);
        $entityType = ArgumentReader::getEntityType($actionEntity);

        if ($entityId === null || $entityType === null) {
            throw new InvalidArgumentException('Arguments "entity_id" and "entity_type" are required.');
        }

        return [
            ArgumentReader::ENTITY_TYPE => $this->exportEntityManagement->create(
                $entityId,
                $entityType,
                ExportArgumentReader::getFileName($actionEntity)
            )
        ];
    }
}
