<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Exception;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Phrase;
use Magento\Framework\Stdlib\DateTime;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Api\Data\ExportEntityInterfaceFactory;
use Opengento\Gdpr\Api\ExportEntityCheckerInterface;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;
use Opengento\Gdpr\Model\Archive\MoveToArchive;
use Opengento\Gdpr\Service\Export\ProcessorFactory;
use Opengento\Gdpr\Service\Export\RendererFactory;
use function sha1;
use const DIRECTORY_SEPARATOR;

final class ExportEntityManagement implements ExportEntityManagementInterface
{
    /**
     * @var ExportEntityInterfaceFactory
     */
    private $exportEntityFactory;

    /**
     * @var ExportEntityRepositoryInterface
     */
    private $exportEntityRepository;

    /**
     * @var ExportEntityCheckerInterface
     */
    private $exportEntityChecker;

    /**
     * @var ProcessorFactory
     */
    private $exportProcessorFactory;

    /**
     * @var RendererFactory
     */
    private $exportRendererFactory;

    /**
     * @var MoveToArchive
     */
    private $archive;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        ExportEntityInterfaceFactory $exportEntityFactory,
        ExportEntityRepositoryInterface $exportEntityRepository,
        ExportEntityCheckerInterface $exportEntityChecker,
        ProcessorFactory $exportProcessorFactory,
        RendererFactory $exportRendererFactory,
        MoveToArchive $archive,
        Config $config
    ) {
        $this->exportEntityFactory = $exportEntityFactory;
        $this->exportEntityRepository = $exportEntityRepository;
        $this->exportEntityChecker = $exportEntityChecker;
        $this->exportProcessorFactory = $exportProcessorFactory;
        $this->exportRendererFactory = $exportRendererFactory;
        $this->archive = $archive;
        $this->config = $config;
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
        $exportEntity->setFileName($fileName ?? $this->config->getExportFileName());
        $exportEntity = $this->exportEntityRepository->save($exportEntity);

        return $exportEntity;
    }

    /**
     * @inheritdoc
     * @throws FileSystemException
     * @throws NotFoundException
     * @throws Exception
     */
    public function export(ExportEntityInterface $exportEntity): string
    {
        $exporter = $this->exportProcessorFactory->get($exportEntity->getEntityType());
        $fileName = $this->prepareFileName($exportEntity);
        $data = $exporter->execute($exportEntity->getEntityId(), []);
        foreach ($this->config->getExportRendererCodes() as $rendererCode) {
            $filePath = $this->archive->prepareArchive(
                $this->exportRendererFactory->get($rendererCode)->saveData($fileName, $data),
                $fileName . '.zip'
            );
        }
        //todo remove files after bundling

        if (!isset($filePath)) {
            throw new LocalizedException(
                new Phrase(
                    'The archive cannot be created for the entity type %& with ID %2.',
                    [$exportEntity->getEntityType(), $exportEntity->getEntityId()]
                )
            );
        }

        $exportEntity->setFilePath($filePath);
        $exportEntity->setExpiredAt(
            (new \DateTime('+' . $this->config->getExportLifetime() . 'minutes'))->format(DateTime::DATETIME_PHP_FORMAT)
        );
        $exportEntity->setExportedAt(
            (new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT)
        );
        $this->exportEntityRepository->save($exportEntity);

        return $filePath;
    }

    private function prepareFileName(ExportEntityInterface $exportEntity): string
    {
        return 'gdpr' .
            DIRECTORY_SEPARATOR .
            sha1($exportEntity->getEntityType() . $exportEntity->getExportId()) .
            DIRECTORY_SEPARATOR .
            $exportEntity->getFileName();
    }
}
