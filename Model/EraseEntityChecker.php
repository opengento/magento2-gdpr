<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Opengento\Gdpr\Api\Data\EraseEntityInterface;
use Opengento\Gdpr\Api\EraseEntityCheckerInterface;
use Opengento\Gdpr\Api\EraseEntityRepositoryInterface;
use Opengento\Gdpr\Model\Entity\EntityCheckerFactory;

final class EraseEntityChecker implements EraseEntityCheckerInterface
{
    /**
     * @var EraseEntityRepositoryInterface
     */
    private $eraseRepository;

    /**
     * @var EntityCheckerFactory
     */
    private $entityCheckerFactory;

    public function __construct(
        EraseEntityRepositoryInterface $eraseRepository,
        EntityCheckerFactory $entityCheckerFactory
    ) {
        $this->eraseRepository = $eraseRepository;
        $this->entityCheckerFactory = $entityCheckerFactory;
    }

    public function exists(int $entityId, string $entityType): bool
    {
        try {
            return (bool) $this->eraseRepository->getByEntity($entityId, $entityType)->getEraseId();
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    public function canCreate(int $entityId, string $entityType): bool
    {
        $entityChecker = $this->entityCheckerFactory->get($entityType);

        return !$this->exists($entityId, $entityType) && $entityChecker->canErase($entityId);
    }

    public function canCancel(int $entityId, string $entityType): bool
    {
        try {
            $entity = $this->eraseRepository->getByEntity($entityId, $entityType);
        } catch (NoSuchEntityException $e) {
            return false;
        }

        return $entity->getState() === EraseEntityInterface::STATE_PENDING
            && $entity->getStatus() === EraseEntityInterface::STATUS_READY;
    }

    public function canProcess(int $entityId, string $entityType): bool
    {
        try {
            $entity = $this->eraseRepository->getByEntity($entityId, $entityType);
        } catch (NoSuchEntityException $e) {
            return false;
        }
        $entityChecker = $this->entityCheckerFactory->get($entityType);

        return (($entity->getState() === EraseEntityInterface::STATE_PENDING
                && $entity->getStatus() === EraseEntityInterface::STATUS_READY)
            || ($entity->getState() === EraseEntityInterface::STATE_PROCESSING
                && $entity->getStatus() === EraseEntityInterface::STATUS_FAILED))
            && $entityChecker->canErase($entityId);
    }
}
