<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service;

use Magento\Sales\Api\Data\OrderInterface;
use Opengento\Gdpr\Api\ExportGuestInterface;
use Opengento\Gdpr\Service\Export\RendererInterface;
use Opengento\Gdpr\Service\Guest\Export\ProcessorInterface;

/**
 * Class ExportGuestManagement
 */
final class ExportGuestManagement implements ExportGuestInterface
{
    /**
     * @var \Opengento\Gdpr\Service\Guest\Export\ProcessorInterface
     */
    private $exportProcessor;

    /**
     * @var \Opengento\Gdpr\Service\Export\RendererInterface
     */
    private $exportRenderer;

    /**
     * @param \Opengento\Gdpr\Service\Guest\Export\ProcessorInterface $exportProcessor
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
    public function exportToFile(OrderInterface $order, string $fileName): string
    {
        return $this->exportRenderer->saveData($fileName, $this->exportProcessor->execute($order, []));
    }
}
