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
    private ?bool $isExportEntityExists = null;
    private ?bool $isExported = null;

    public function __construct(
        private ExportEntityCheckerInterface $exportEntityChecker,
        private Registry $registry
    ) {}

    public function hasExport(): bool
    {
        return $this->isExportEntityExists ??= $this->exportEntityChecker->exists($this->currentOrderId(), 'order');
    }

    public function isExported(): bool
    {
        return $this->isExported ??= $this->exportEntityChecker->isExported($this->currentOrderId(), 'order');
    }

    private function currentOrderId(): int
    {
        /** @var OrderInterface $order */
        $order = $this->registry->registry('current_order');

        return (int)$order->getEntityId();
    }
}
