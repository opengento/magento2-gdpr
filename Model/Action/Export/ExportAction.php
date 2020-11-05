<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action\Export;

use Magento\Framework\Exception\InputException;
use Opengento\Gdpr\Api\Data\ActionContextInterface;
use Opengento\Gdpr\Api\Data\ActionResultInterface;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Model\Action\AbstractAction;
use Opengento\Gdpr\Model\Action\Export\ArgumentReader as ExportArgumentReader;
use Opengento\Gdpr\Model\Action\ResultBuilder;

final class ExportAction extends AbstractAction
{
    /**
     * @var ExportEntityManagementInterface
     */
    private $entityManagement;

    public function __construct(
        ResultBuilder $resultBuilder,
        ExportEntityManagementInterface $entityManagement
    ) {
        $this->entityManagement = $entityManagement;
        parent::__construct($resultBuilder);
    }

    public function execute(ActionContextInterface $actionContext): ActionResultInterface
    {
        $exportEntity = ArgumentReader::getEntity($actionContext);

        if ($exportEntity === null) {
            throw InputException::requiredField('entity');
        }

        return $this->createActionResult(
            [ExportArgumentReader::EXPORT_ENTITY => $this->entityManagement->export($exportEntity)]
        );
    }
}
