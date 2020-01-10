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
use Opengento\Gdpr\Model\Action\ArgumentReader;
use Opengento\Gdpr\Model\Action\Export\ArgumentReader as ExportArgumentReader;
use Opengento\Gdpr\Model\Action\ResultBuilder;
use function array_reduce;

final class CreateAction extends AbstractAction
{
    /**
     * @var ExportEntityManagementInterface
     */
    private $exporter;

    public function __construct(
        ResultBuilder $resultBuilder,
        ExportEntityManagementInterface $exporter
    ) {
        $this->exporter = $exporter;
        parent::__construct($resultBuilder);
    }

    public function execute(ActionContextInterface $actionContext): ActionResultInterface
    {
        return $this->createActionResult(
            [
                ArgumentReader::ENTITY_TYPE => $this->exporter->create(
                    ...$this->getArguments($actionContext)
                )
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

        return [$entityId, $entityType, ExportArgumentReader::getFileName($actionContext)];
    }
}
