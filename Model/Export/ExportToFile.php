<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Export;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Store\Model\ScopeInterface;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Model\Archive\MoveToArchive;
use Opengento\Gdpr\Service\Export\ProcessorFactory;
use Opengento\Gdpr\Service\Export\RendererFactory;
use function explode;
use function sha1;
use const DIRECTORY_SEPARATOR;

final class ExportToFile
{
    private const CONFIG_PATH_EXPORT_RENDERERS = 'gdpr/export/renderers';

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
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        ProcessorFactory $exportProcessorFactory,
        RendererFactory $exportRendererFactory,
        MoveToArchive $archive,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->exportProcessorFactory = $exportProcessorFactory;
        $this->exportRendererFactory = $exportRendererFactory;
        $this->archive = $archive;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param ExportEntityInterface $exportEntity
     * @return string|null
     * @throws FileSystemException
     * @throws NotFoundException
     */
    public function export(ExportEntityInterface $exportEntity): ?string
    {
        $exporter = $this->exportProcessorFactory->get($exportEntity->getEntityType());
        $fileName = $this->prepareFileName($exportEntity);
        $data = $exporter->execute($exportEntity->getEntityId(), []);
        foreach ($this->resolveExportRendererCodes() as $rendererCode) {
            $filePath = $this->archive->prepareArchive(
                $this->exportRendererFactory->get($rendererCode)->saveData($fileName, $data),
                $fileName . '.zip'
            );
        }

        return $filePath ?? null;
    }

    public function resolveExportRendererCodes(): array
    {
        return explode(',', (string) $this->scopeConfig->getValue(
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
