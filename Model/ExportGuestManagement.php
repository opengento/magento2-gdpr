<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Magento\Sales\Api\Data\OrderInterface;
use Opengento\Gdpr\Api\ExportGuestInterface;
use Opengento\Gdpr\Service\Export\ProcessorInterface;
use Opengento\Gdpr\Service\Export\RendererInterface;

/**
 * Class ExportGuestManagement
 */
final class ExportGuestManagement implements ExportGuestInterface
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
    public function exportToFile(OrderInterface $order, string $fileName): string
    {
        return $this->exportRenderer->saveData($fileName, $this->exportProcessor->execute((int) $order->getEntityId(), []));
    }
}
