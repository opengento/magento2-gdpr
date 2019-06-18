<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Opengento\Gdpr\Api\ExportInterface;
use Opengento\Gdpr\Service\Export\ProcessorInterface;
use Opengento\Gdpr\Service\Export\RendererInterface;

/**
 * Class ExportManagement
 */
final class ExportManagement implements ExportInterface
{
    /**
     * @var \Opengento\Gdpr\Service\Export\ProcessorInterface
     */
    private $exportProcessor;

    /**
     * @var \Opengento\Gdpr\Service\Export\RendererInterface
     */
    private $exportRenderer;

    /**
     * @param \Opengento\Gdpr\Service\Export\ProcessorInterface $exportProcessor
     * @param \Opengento\Gdpr\Service\Export\RendererInterface $exportRenderer
     */
    public function __construct(
        ProcessorInterface $exportProcessor,
        RendererInterface $exportRenderer
    ) {
        $this->exportProcessor = $exportProcessor;
        $this->exportRenderer = $exportRenderer;
    }

    /**
     * @inheritdoc
     */
    public function exportToFile(int $customerId, string $fileName): string
    {
        return $this->exportRenderer->saveData($fileName, $this->exportProcessor->execute($customerId, []));
    }
}
