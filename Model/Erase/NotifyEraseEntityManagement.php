<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Erase;

use Opengento\Gdpr\Api\Data\EraseEntityInterface;
use Opengento\Gdpr\Api\EraseEntityManagementInterface;
use Opengento\Gdpr\Api\EraseEntityRepositoryInterface;

class NotifyEraseEntityManagement implements EraseEntityManagementInterface
{
    public function __construct(
        private EraseEntityManagementInterface $eraseManagement,
        private EraseEntityRepositoryInterface $eraseRepository,
        private NotifierRepository $notifierRepository
    ) {}

    public function create(int $entityId, string $entityType): EraseEntityInterface
    {
        $eraseEntity = $this->eraseManagement->create($entityId, $entityType);
        $this->notifierRepository->get($entityType, 'pending')->notify($eraseEntity);

        return $eraseEntity;
    }

    public function cancel(int $entityId, string $entityType): bool
    {
        $eraseEntity = $this->eraseRepository->getByEntity($entityId, $entityType);
        $canceled = $this->eraseManagement->cancel($entityId, $entityType);
        if ($canceled) {
            $this->notifierRepository->get($entityType, 'cancel')->notify($eraseEntity);
        }

        return $canceled;
    }

    public function process(EraseEntityInterface $entity): EraseEntityInterface
    {
        $eraseEntity = $this->eraseManagement->process($entity);
        $this->notifierRepository->get($entity->getEntityType(), 'success')->notify($eraseEntity);

        return $eraseEntity;
    }
}
