<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Erase;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\Data\EraseEntityInterface;
use Opengento\Gdpr\Api\EraseEntityCheckerInterface;
use Opengento\Gdpr\Api\EraseEntityManagementInterface;

final class SecureEraseEntityManagement implements EraseEntityManagementInterface
{
    private EraseEntityManagementInterface $eraseManagement;

    private EraseEntityCheckerInterface $eraseEntityChecker;

    public function __construct(
        EraseEntityManagementInterface $eraseManagement,
        EraseEntityCheckerInterface $eraseEntityChecker
    ) {
        $this->eraseManagement = $eraseManagement;
        $this->eraseEntityChecker = $eraseEntityChecker;
    }

    public function create(int $entityId, string $entityType): EraseEntityInterface
    {
        if ($this->eraseEntityChecker->canCreate($entityId, $entityType)) {
            return $this->eraseManagement->create($entityId, $entityType);
        }

        throw new LocalizedException(
            new Phrase(
                'Impossible to initiate the erasure, it\'s already processing or there is still pending orders.'
            )
        );
    }

    public function cancel(int $entityId, string $entityType): bool
    {
        if ($this->eraseEntityChecker->canCancel($entityId, $entityType)) {
            return $this->eraseManagement->cancel($entityId, $entityType);
        }

        throw new LocalizedException(new Phrase('The erasure process is running and cannot be undone.'));
    }

    public function process(EraseEntityInterface $entity): EraseEntityInterface
    {
        if ($this->eraseEntityChecker->canProcess($entity->getEntityId(), $entity->getEntityType())) {
            return $this->eraseManagement->process($entity);
        }

        throw new LocalizedException(new Phrase('Impossible to process the erasure, there is still pending orders.'));
    }
}
