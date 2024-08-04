<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Export;

use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;

class NotifyExportEntityManagement implements ExportEntityManagementInterface
{
    public function __construct(
        private ExportEntityManagementInterface $exportManagement,
        private NotifierRepository $notifierRepository
    ) {}

    public function create(int $entityId, string $entityType, ?string $fileName = null): ExportEntityInterface
    {
        $exportEntity = $this->exportManagement->create($entityId, $entityType, $fileName);
        $this->notifierRepository->get($entityType, 'pending')->notify($exportEntity);

        return $exportEntity;
    }

    public function export(ExportEntityInterface $exportEntity): ExportEntityInterface
    {
        $exportEntity = $this->exportManagement->export($exportEntity);
        $this->notifierRepository->get($exportEntity->getEntityType(), 'ready')->notify($exportEntity);


        return $exportEntity;
    }

    public function invalidate(ExportEntityInterface $exportEntity): ExportEntityInterface
    {
        return $this->exportManagement->invalidate($exportEntity);
    }
}
