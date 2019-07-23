<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Erase\Notifier;

use Opengento\Gdpr\Api\Data\EraseEntityInterface;
use Opengento\Gdpr\Api\EraseEntityManagementInterface;
use Opengento\Gdpr\Api\EraseEntityRepositoryInterface;
use Opengento\Gdpr\Model\Erase\NotifierFactory;

/**
 * Class EraseEntityManagement
 */
final class EraseEntityManagement implements EraseEntityManagementInterface
{
    /**
     * @var \Opengento\Gdpr\Api\EraseEntityRepositoryInterface
     */
    private $eraseRepository;

    /**
     * @var \Opengento\Gdpr\Api\EraseEntityManagementInterface
     */
    private $eraseManagement;

    /**
     * @var \Opengento\Gdpr\Model\Erase\NotifierFactory
     */
    private $eraseNotifierFactory;

    /**
     * @param \Opengento\Gdpr\Api\EraseEntityRepositoryInterface $eraseRepository
     * @param \Opengento\Gdpr\Api\EraseEntityManagementInterface $eraseManagement
     * @param \Opengento\Gdpr\Model\Erase\NotifierFactory $eraseNotifierFactory
     */
    public function __construct(
        EraseEntityRepositoryInterface $eraseRepository,
        EraseEntityManagementInterface $eraseManagement,
        NotifierFactory $eraseNotifierFactory
    ) {
        $this->eraseRepository = $eraseRepository;
        $this->eraseManagement = $eraseManagement;
        $this->eraseNotifierFactory = $eraseNotifierFactory;
    }

    /**
     * @inheritdoc
     */
    public function create(int $entityId, string $entityType): EraseEntityInterface
    {
        $entity = $this->eraseManagement->create($entityId, $entityType);
        $this->eraseNotifierFactory->get('pending', $entityType)->notify($entity);

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function process(EraseEntityInterface $entity): EraseEntityInterface
    {
        $entity = $this->eraseManagement->process($entity);
        $this->eraseNotifierFactory->get('succeeded', $entity->getEntityType())->notify($entity);

        return $entity;
    }

    /**
     * @inheritdoc
     */
    public function cancel(int $entityId, string $entityType): bool
    {
        $entity = $this->eraseRepository->getByEntity($entityId, $entityType);
        $isCanceled = $this->eraseManagement->cancel($entityId, $entityType);

        if ($isCanceled) {
            $this->eraseNotifierFactory->get('canceled', $entityType)->notify($entity);
        }

        return $isCanceled;
    }
}
