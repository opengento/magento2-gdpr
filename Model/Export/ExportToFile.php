<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Export;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Store\Model\ScopeInterface;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Model\Archive\ArchiveManager;
use Opengento\Gdpr\Service\Export\ProcessorFactory;
use Opengento\Gdpr\Service\Export\RendererFactory;

use function explode;
use function sha1;

use const DIRECTORY_SEPARATOR;

class ExportToFile
{
    private const CONFIG_PATH_EXPORT_RENDERERS = 'gdpr/export/renderers';

    private ProcessorFactory $processorFactory;

    private RendererFactory $rendererFactory;

    private ArchiveManager $archiveManager;

    private ScopeConfigInterface $scopeConfig;

    public function __construct(
        ProcessorFactory $processorFactory,
        RendererFactory $rendererFactory,
        ArchiveManager $archiveManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->processorFactory = $processorFactory;
        $this->rendererFactory = $rendererFactory;
        $this->archiveManager = $archiveManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param ExportEntityInterface $exportEntity
     * @return string|null
     * @throws FileSystemException
     * @throws NotFoundException
     * @throws NoSuchEntityException
     */
    public function export(ExportEntityInterface $exportEntity): string
    {
        $fileName = $this->prepareFileName($exportEntity);
        $archiveFileName = $fileName . '.zip';
        $data = $this->processorFactory->get($exportEntity->getEntityType())->execute($exportEntity->getEntityId(), []);

        foreach ($this->resolveExportRendererCodes() as $rendererCode) {
            $this->archiveManager->addToArchive(
                $this->rendererFactory->get($rendererCode)->saveData($fileName, $data),
                $archiveFileName
            );
        }

        return $archiveFileName;
    }

    public function resolveExportRendererCodes(): array
    {
        return explode(',', (string)$this->scopeConfig->getValue(
            self::CONFIG_PATH_EXPORT_RENDERERS,
            ScopeInterface::SCOPE_STORE
        ));
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
