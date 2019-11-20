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
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Model\Action\AbstractAction;
use Opengento\Gdpr\Model\Action\ArgumentReader;
use Opengento\Gdpr\Model\Action\Export\ArgumentReader as ExportArgumentReader;
use Opengento\Gdpr\Model\Action\ResultBuilder;

final class CreateAction extends AbstractAction
{
    /**
     * @var ExportEntityManagementInterface
     */
    private $exportEntityManagement;

    public function __construct(
        ResultBuilder $resultBuilder,
        ExportEntityManagementInterface $exportEntityManagement
    ) {
        $this->exportEntityManagement = $exportEntityManagement;
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
            [
                ArgumentReader::ENTITY_TYPE => $this->exportEntityManagement->create(
                    $entityId,
                    $entityType,
                    ExportArgumentReader::getFileName($actionContext)
                )
            ]
        );
    }
}
