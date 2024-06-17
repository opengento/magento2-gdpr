<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\ViewModel\Customer\Guest;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Opengento\Gdpr\Api\ExportEntityCheckerInterface;

class ExportDataProvider implements ArgumentInterface
{
    private ExportEntityCheckerInterface $exportEntityChecker;

    private Registry $registry;

    /**
     * @var bool|null
     */
    private ?bool $isExportEntityExists;

    /**
     * @var bool|null
     */
    private ?bool $isExported;

    public function __construct(
        ExportEntityCheckerInterface $exportEntityChecker,
        Registry $registry
    ) {
        $this->exportEntityChecker = $exportEntityChecker;
        $this->registry = $registry;
    }

    public function hasExport(): bool
    {
        return $this->isExportEntityExists ??
            $this->isExportEntityExists = $this->exportEntityChecker->exists($this->currentOrderId(), 'order');
    }

    public function isExported(): bool
    {
        return $this->isExported ??
            $this->isExported = $this->exportEntityChecker->isExported($this->currentOrderId(), 'order');
    }

    private function currentOrderId(): int
    {
        /** @var OrderInterface $order */
        $order = $this->registry->registry('current_order');

        return (int) $order->getEntityId();
    }
}
