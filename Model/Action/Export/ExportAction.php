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
use Opengento\Gdpr\Model\Action\ResultBuilder;

final class ExportAction extends AbstractAction
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
        $exportEntity = ArgumentReader::getEntity($actionContext);

        if ($exportEntity === null) {
            throw new InvalidArgumentException('Argument "entity" is required.');
        }

        return $this->createActionResult(
            ['export_file_path' => $this->exportEntityManagement->export($exportEntity)]
        );
    }
}
