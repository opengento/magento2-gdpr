<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action\Export;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Opengento\Gdpr\Api\Data\ActionContextInterface;
use Opengento\Gdpr\Api\Data\ActionResultInterface;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;
use Opengento\Gdpr\Model\Action\AbstractAction;
use Opengento\Gdpr\Model\Action\Export\ArgumentReader as ExportArgumentReader;
use Opengento\Gdpr\Model\Action\ResultBuilder;

final class ExportAction extends AbstractAction
{
    private ExportEntityRepositoryInterface $exportRepository;

    private ExportEntityManagementInterface $exportManagement;

    public function __construct(
        ResultBuilder $resultBuilder,
        ExportEntityRepositoryInterface $exportRepository,
        ExportEntityManagementInterface $exportManagement
    ) {
        $this->exportRepository = $exportRepository;
        $this->exportManagement = $exportManagement;
        parent::__construct($resultBuilder);
    }

    public function execute(ActionContextInterface $actionContext): ActionResultInterface
    {
        $exportEntity = ArgumentReader::getEntity($actionContext);

        if ($exportEntity === null) {
            throw InputException::requiredField('entity');
        }

        try {
            $exportEntity = $this->exportManagement->export($exportEntity);
        } catch (NoSuchEntityException $e) {
            $this->exportRepository->delete($exportEntity);

            throw $e;
        }

        return $this->createActionResult(
            [ExportArgumentReader::EXPORT_ENTITY => $exportEntity]
        );
    }
}
