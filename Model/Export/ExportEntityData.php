<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Export;

use Magento\Framework\Exception\NoSuchEntityException;
use Opengento\Gdpr\Api\ExportEntityCheckerInterface;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;

/**
 * Class ExportEntityData
 */
final class ExportEntityData
{
    /**
     * @var ExportEntityRepositoryInterface
     */
    private $exportEntityRepository;

    /**
     * @var ExportEntityManagementInterface
     */
    private $exportEntityManagement;

    /**
     * @var ExportEntityCheckerInterface
     */
    private $exportEntityChecker;

    /**
     * @param ExportEntityRepositoryInterface $exportEntityRepository
     * @param ExportEntityManagementInterface $exportEntityManagement
     * @param ExportEntityCheckerInterface $exportEntityChecker
     */
    public function __construct(
        ExportEntityRepositoryInterface $exportEntityRepository,
        ExportEntityManagementInterface $exportEntityManagement,
        ExportEntityCheckerInterface $exportEntityChecker
    ) {
        $this->exportEntityRepository = $exportEntityRepository;
        $this->exportEntityManagement = $exportEntityManagement;
        $this->exportEntityChecker = $exportEntityChecker;
    }

    /**
     * Export the entity to a file
     *
     * @param int $entityId
     * @param string $entityType
     * @return string
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function export(int $entityId, string $entityType): string
    {
        try {
            $exportEntity = $this->exportEntityRepository->getByEntity($entityId, $entityType);
        } catch (NoSuchEntityException $e) {
            $exportEntity = $this->exportEntityManagement->create($entityId, $entityType);
        }

        return $this->exportEntityChecker->isExported($entityId, $entityType)
            ? $exportEntity->getFilePath()
            : $this->exportEntityManagement->export($exportEntity);
    }
}
