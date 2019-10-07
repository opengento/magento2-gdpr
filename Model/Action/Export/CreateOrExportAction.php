<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action\Export;

use InvalidArgumentException;
use Opengento\Gdpr\Api\Data\ActionContextInterface;
use Opengento\Gdpr\Api\Data\ActionResultInterface;
use Opengento\Gdpr\Model\Action\AbstractAction;
use Opengento\Gdpr\Model\Action\ArgumentReader;
use Opengento\Gdpr\Model\Action\ResultBuilder;
use Opengento\Gdpr\Model\Export\ExportEntityData;

final class CreateOrExportAction extends AbstractAction
{
    /**
     * @var ExportEntityData
     */
    private $exportEntityData;

    public function __construct(
        ResultBuilder $resultBuilder,
        ExportEntityData $exportEntityData
    ) {
        $this->exportEntityData = $exportEntityData;
        parent::__construct($resultBuilder);
    }

    public function execute(ActionContextInterface $actionContext): ActionResultInterface
    {
        $entityId = ArgumentReader::getEntityId($actionContext);
        $entityType = ArgumentReader::getEntityType($actionContext);

        if ($entityId === null || $entityType === null) {
            throw new InvalidArgumentException('Arguments "entity_id" and "entity_type" are required.');
        }

        return $this->createActionResult(
            ['export_file_path' => $this->exportEntityData->export($entityId, $entityType)]
        );
    }
}
