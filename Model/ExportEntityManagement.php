<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Phrase;
use Magento\Framework\Stdlib\DateTime;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Api\Data\ExportEntityInterfaceFactory;
use Opengento\Gdpr\Api\ExportEntityCheckerInterface;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;
use Opengento\Gdpr\Service\Export\ProcessorFactory;
use Opengento\Gdpr\Service\Export\RendererInterface;
use function md5;
use function sha1;
use const DIRECTORY_SEPARATOR;

/**
 * Class ExportEntityManagement
 */
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
     * @var \Opengento\Gdpr\Service\Export\ProcessorFactory
     */
    private $exportProcessorFactory;

    /**
     * @var \Opengento\Gdpr\Service\Export\RendererInterface
     */
    private $exportRenderer;

    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @param ExportEntityInterfaceFactory $exportEntityFactory
     * @param ExportEntityRepositoryInterface $exportEntityRepository
     * @param ExportEntityCheckerInterface $exportEntityChecker
     * @param ProcessorFactory $exportProcessorFactory
     * @param RendererInterface $exportRenderer
     * @param Config $config
     */
    public function __construct(
        ExportEntityInterfaceFactory $exportEntityFactory,
        ExportEntityRepositoryInterface $exportEntityRepository,
        ExportEntityCheckerInterface $exportEntityChecker,
        ProcessorFactory $exportProcessorFactory,
        RendererInterface $exportRenderer,
        Config $config
    ) {
        $this->exportEntityFactory = $exportEntityFactory;
        $this->exportEntityRepository = $exportEntityRepository;
        $this->exportEntityChecker = $exportEntityChecker;
        $this->exportProcessorFactory = $exportProcessorFactory;
        $this->exportRenderer = $exportRenderer;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
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
     */
    public function export(ExportEntityInterface $exportEntity): string
    {
        $exporter = $this->exportProcessorFactory->get($exportEntity->getEntityType());
        $filePath = $this->exportRenderer->saveData(
            $this->prepareFileName($exportEntity),
            $exporter->execute($exportEntity->getEntityId(), [])
        );
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

    /**
     * Prepare the file name with sub directory for the export entity
     *
     * @param ExportEntityInterface $exportEntity
     * @return string
     */
    private function prepareFileName(ExportEntityInterface $exportEntity): string
    {
        return 'gdpr' .
            DIRECTORY_SEPARATOR .
            sha1($exportEntity->getEntityType() . $exportEntity->getExportId()) .
            DIRECTORY_SEPARATOR .
            $exportEntity->getFileName();
    }
}
