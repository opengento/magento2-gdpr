<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\ViewModel\Privacy;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Opengento\Gdpr\Api\ExportEntityCheckerInterface;

/**
 * Class ExportGuestDataProvider
 */
final class ExportGuestDataProvider implements ArgumentInterface
{
    /**
     * @var ExportEntityCheckerInterface
     */
    private $exportEntityChecker;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var null|bool
     */
    private $isExportEntityExists;

    /**
     * @var null|bool
     */
    private $isExported;

    /**
     * @param ExportEntityCheckerInterface $exportEntityChecker
     * @param Registry $registry
     */
    public function __construct(
        ExportEntityCheckerInterface $exportEntityChecker,
        Registry $registry
    ) {
        $this->exportEntityChecker = $exportEntityChecker;
        $this->registry = $registry;
    }

    /**
     * Check if the export entity exists for the current guest
     *
     * @return bool
     */
    public function hasExport(): bool
    {
        return $this->isExportEntityExists ??
            $this->isExportEntityExists = $this->exportEntityChecker->exists($this->resolveOrderId(), 'order');
    }

    /**
     * Check if the export entity is ready for download
     *
     * @return bool
     */
    public function isExported(): bool
    {
        return $this->isExported ??
            $this->isExported = $this->exportEntityChecker->isExported($this->resolveOrderId(), 'order');
    }

    /**
     * Resolve the current order ID
     *
     * @return int
     */
    private function resolveOrderId(): int
    {
        /** @var OrderInterface $order */
        $order = $this->registry->registry('current_order');

        return (int) $order->getEntityId();
    }
}
