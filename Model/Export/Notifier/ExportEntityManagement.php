<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Export\Notifier;

use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Model\Export\NotifierFactory;

final class ExportEntityManagement implements ExportEntityManagementInterface
{
    /**
     * @var ExportEntityManagementInterface
     */
    private $exportManagement;

    /**
     * @var NotifierFactory
     */
    private $exportNotifierFactory;

    public function __construct(
        ExportEntityManagementInterface $exportManagement,
        NotifierFactory $exportNotifierFactory
    ) {
        $this->exportManagement = $exportManagement;
        $this->exportNotifierFactory = $exportNotifierFactory;
    }

    public function create(int $entityId, string $entityType, ?string $fileName = null): ExportEntityInterface
    {
        return $this->exportManagement->create($entityId, $entityType);
    }

    public function export(ExportEntityInterface $exportEntity): string
    {
        $export = $this->exportManagement->export($exportEntity);
        $this->exportNotifierFactory->get('succeeded', $exportEntity->getEntityType())->notify($exportEntity);

        return $export;
    }
}
