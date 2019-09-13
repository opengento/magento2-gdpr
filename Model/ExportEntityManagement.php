<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Api\Data\ExportEntityInterfaceFactory;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;
use Opengento\Gdpr\Service\Export\ProcessorFactory;
use Opengento\Gdpr\Service\Export\RendererInterface;

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
     * @param ProcessorFactory $exportProcessorFactory
     * @param RendererInterface $exportRenderer
     * @param \Opengento\Gdpr\Model\Config $config
     */
    public function __construct(
        ExportEntityInterfaceFactory $exportEntityFactory,
        ExportEntityRepositoryInterface $exportEntityRepository,
        ProcessorFactory $exportProcessorFactory,
        RendererInterface $exportRenderer,
        Config $config
    ) {
        $this->exportEntityFactory = $exportEntityFactory;
        $this->exportEntityRepository = $exportEntityRepository;
        $this->exportProcessorFactory = $exportProcessorFactory;
        $this->exportRenderer = $exportRenderer;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function create(int $entityId, string $entityType, ?string $fileName = null): ExportEntityInterface
    {
        try {
            $exportEntity = $this->exportEntityRepository->getByEntity($entityId, $entityType);
        } catch (NoSuchEntityException $e) {
            /** @var ExportEntityInterface $exportEntity */
            $exportEntity = $this->exportEntityFactory->create();
            $exportEntity->setEntityId($entityId);
            $exportEntity->setEntityType($entityType);
            $exportEntity->setFileName($fileName ?? $this->config->getExportFileName());
            $exportEntity = $this->exportEntityRepository->save($exportEntity);
        }

        return $exportEntity;
    }

    /**
     * @inheritdoc
     */
    public function export(ExportEntityInterface $exportEntity): string
    {
        if ($exportEntity->getFilePath()) {
            return $exportEntity->getFilePath();
        }

        $exporter = $this->exportProcessorFactory->get($exportEntity->getEntityType());
        $filePath = $this->exportRenderer->saveData(
            $exportEntity->getFileName(),
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
}
