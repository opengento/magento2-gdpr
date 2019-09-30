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
use Opengento\Gdpr\Model\Action\ProcessorInterface;

final class ExportProcessor implements ProcessorInterface
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
        $exportEntity = ArgumentReader::getEntity($actionEntity);

        if ($exportEntity === null) {
            throw new InvalidArgumentException('Argument "entity" is required.');
        }

        return ['export_file_path' => $this->exportEntityManagement->export($exportEntity)];
    }
}
