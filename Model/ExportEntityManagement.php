<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Service\Export\ProcessorFactory;
use Opengento\Gdpr\Service\Export\RendererInterface;

/**
 * Class ExportEntityManagement
 */
final class ExportEntityManagement implements ExportEntityManagementInterface
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
    public function export(ExportEntityInterface $exportEntity): string
    {
        $exporter = $this->exportProcessorFactory->get($exportEntity->getEntityType());

        return $this->exportRenderer->saveData(
            $exportEntity->getFileName(),
            $exporter->execute($exportEntity->getEntityId(), [])
        );
    }
}
