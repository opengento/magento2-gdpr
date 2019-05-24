<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service;

use Opengento\Gdpr\Service\Export\ProcessorInterface;
use Opengento\Gdpr\Service\Export\RendererInterface;

/**
 * Class ExportManagement
 * @api
 */
final class ExportManagement
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
     * Export all data related to a given entity ID to the file
     *
     * @param int $customerId
     * @param string $fileName
     * @return string
     */
    public function exportToFile(int $customerId, string $fileName): string
    {
        return $this->exportRenderer->saveData($fileName, $this->exportProcessor->execute($customerId, []));
    }
}
