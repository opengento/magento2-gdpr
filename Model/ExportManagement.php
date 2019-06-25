<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Opengento\Gdpr\Api\ExportInterface;
use Opengento\Gdpr\Service\Export\ProcessorFactory;
use Opengento\Gdpr\Service\Export\RendererInterface;

/**
 * Class ExportManagement
 */
final class ExportManagement implements ExportInterface
{
    /**
     * @var \Opengento\Gdpr\Service\Export\ProcessorFactory
     */
    private $exportProcessorFactory;

    /**
     * @var \Opengento\Gdpr\Service\Export\RendererInterface
     */
    private $exportRenderer;

    /**
     * @param \Opengento\Gdpr\Service\Export\ProcessorFactory $exportProcessorFactory
     * @param \Opengento\Gdpr\Service\Export\RendererInterface $exportRenderer
     */
    public function __construct(
        ProcessorFactory $exportProcessorFactory,
        RendererInterface $exportRenderer
    ) {
        $this->exportProcessorFactory = $exportProcessorFactory;
        $this->exportRenderer = $exportRenderer;
    }

    /**
     * @inheritdoc
     */
    public function exportToFile(int $entityId, string $entityType, string $fileName): string
    {
        $exporter = $this->exportProcessorFactory->get($entityType);

        return $this->exportRenderer->saveData($fileName, $exporter->execute($entityId, []));
    }
}
