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
use Opengento\Gdpr\Model\Action\AbstractAction;
use Opengento\Gdpr\Model\Action\ArgumentReader;
use Opengento\Gdpr\Model\Action\Export\ArgumentReader as ExportArgumentReader;
use Opengento\Gdpr\Model\Action\ResultBuilder;
use Opengento\Gdpr\Model\Export\ExportEntityData;
use function array_reduce;

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
        return $this->createActionResult(
            [
                ExportArgumentReader::EXPORT_ENTITY => $this->exportEntityData->export(
                    ...$this->getArguments($actionContext)
                ),
            ]
        );
    }

    private function getArguments(ActionContextInterface $actionContext): array
    {
        $entityId = ArgumentReader::getEntityId($actionContext);
        $entityType = ArgumentReader::getEntityType($actionContext);
        $errors = [];

        if ($entityId === null) {
            $errors[] = InputException::requiredField('entity_id');
        }
        if ($entityType === null) {
            $errors[] = InputException::requiredField('entity_type');
        }
        if (!empty($errors)) {
            throw array_reduce(
                $errors,
                static function (InputException $aggregated, InputException $input): InputException {
                    return $aggregated->addException($input);
                },
                new InputException()
            );
        }

        return [$entityId, $entityType];
    }
}
