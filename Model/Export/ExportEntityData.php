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
class ExportEntityData
{
    private ExportEntityRepositoryInterface $exportRepository;

    private ExportEntityManagementInterface $exportManagement;

    private ExportEntityCheckerInterface $exportEntityChecker;

    public function __construct(
        ExportEntityRepositoryInterface $exportRepository,
        ExportEntityManagementInterface $exportManagement,
        ExportEntityCheckerInterface $exportEntityChecker
    ) {
        $this->exportRepository = $exportRepository;
        $this->exportManagement = $exportManagement;
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
            $exportEntity = $this->exportRepository->getByEntity($entityId, $entityType);
        } catch (NoSuchEntityException $e) {
            $exportEntity = $this->exportManagement->create($entityId, $entityType);
        }

        if (!$this->exportEntityChecker->isExported($entityId, $entityType)) {
            try {
                $exportEntity = $this->exportManagement->export($exportEntity);
            } catch (NoSuchEntityException $e) {
                $this->exportRepository->delete($exportEntity);

                throw $e;
            }
        }

        return $exportEntity;
    }
}
