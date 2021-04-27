<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use DateTime;
use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\Stdlib\DateTime as DateTimeFormat;
use Magento\Store\Model\ScopeInterface;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Api\Data\ExportEntityInterfaceFactory;
use Opengento\Gdpr\Api\ExportEntityCheckerInterface;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;
use Opengento\Gdpr\Model\Export\ExportToFile;

final class ExportEntityManagement implements ExportEntityManagementInterface
{
    private const CONFIG_PATH_EXPORT_FILE_NAME = 'gdpr/export/file_name';
    private const CONFIG_PATH_EXPORT_LIFE_TIME = 'gdpr/export/life_time';

    /**
     * @var ExportEntityInterfaceFactory
     */
    private $exportEntityFactory;

    /**
     * @var ExportEntityRepositoryInterface
     */
    private $exportRepository;

    /**
     * @var ExportEntityCheckerInterface
     */
    private $exportEntityChecker;

    /**
     * @var ExportToFile
     */
    private $exportToFile;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        ExportEntityInterfaceFactory $exportEntityFactory,
        ExportEntityRepositoryInterface $exportRepository,
        ExportEntityCheckerInterface $exportEntityChecker,
        ExportToFile $exportToFile,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->exportEntityFactory = $exportEntityFactory;
        $this->exportRepository = $exportRepository;
        $this->exportEntityChecker = $exportEntityChecker;
        $this->exportToFile = $exportToFile;
        $this->scopeConfig = $scopeConfig;
    }

    public function create(int $entityId, string $entityType, ?string $fileName = null): ExportEntityInterface
    {
        if ($this->exportEntityChecker->exists($entityId, $entityType)) {
            throw new AlreadyExistsException(
                new Phrase(
                    'An export entity already exists for the entity type "%1" with ID "%2".',
                    [$entityType, $entityId]
                )
            );
        }

        /** @var ExportEntityInterface $exportEntity */
        $exportEntity = $this->exportEntityFactory->create();
        $exportEntity->setEntityId($entityId);
        $exportEntity->setEntityType($entityType);
        $exportEntity->setFileName($fileName ?? $this->resolveDefaultFileName());
        $exportEntity = $this->exportRepository->save($exportEntity);

        return $exportEntity;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function export(ExportEntityInterface $exportEntity): ExportEntityInterface
    {
        $lifeTime = (int) $this->scopeConfig->getValue(self::CONFIG_PATH_EXPORT_LIFE_TIME, ScopeInterface::SCOPE_STORE);
        $exportEntity->setFilePath($this->exportToFile->export($exportEntity));
        $exportEntity->setExpiredAt(
            (new DateTime('+' . $lifeTime . 'minutes'))->format(DateTimeFormat::DATETIME_PHP_FORMAT)
        );
        $exportEntity->setExportedAt((new DateTime())->format(DateTimeFormat::DATETIME_PHP_FORMAT));
        $this->exportRepository->save($exportEntity);

        return $exportEntity;
    }

    public function invalidate(ExportEntityInterface $exportEntity): ExportEntityInterface
    {
        $this->exportRepository->delete($exportEntity);

        return $this->create($exportEntity->getEntityId(), $exportEntity->getEntityType(), $exportEntity->getFileName());
    }

    private function resolveDefaultFileName(): string
    {
        return (string) $this->scopeConfig->getValue(self::CONFIG_PATH_EXPORT_FILE_NAME, ScopeInterface::SCOPE_STORE);
    }
}
