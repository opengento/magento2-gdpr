<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Export;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Api\ExportEntityCheckerInterface;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;

/**
 * @api
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
     * Export the entity
     *
     * @param int $entityId
     * @param string $entityType
     * @return ExportEntityInterface
     * @throws AlreadyExistsException
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function export(int $entityId, string $entityType): ExportEntityInterface
    {
        try {
            $exportEntity = $this->exportEntityRepository->getByEntity($entityId, $entityType);
        } catch (NoSuchEntityException $e) {
            $exportEntity = $this->exportEntityManagement->create($entityId, $entityType);
        }

        return $this->exportEntityChecker->isExported($entityId, $entityType)
            ? $exportEntity
            : $this->exportEntityManagement->export($exportEntity);
    }
}
