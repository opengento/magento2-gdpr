<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\ViewModel\Customer\Privacy;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Opengento\Gdpr\Api\ExportEntityCheckerInterface;

class ExportCustomerDataProvider implements ArgumentInterface
{
    private ?bool $isExportEntityExists = null;
    private ?bool $isExported = null;

    public function __construct(
        private ExportEntityCheckerInterface $exportEntityChecker,
        private Session $session
    ) {}

    public function hasExport(): bool
    {
        return $this->isExportEntityExists ??= $this->exportEntityChecker->exists($this->currentCustomerId(), 'customer');
    }

    public function isExported(): bool
    {
        return $this->isExported ??= $this->exportEntityChecker->isExported($this->currentCustomerId(), 'customer');
    }

    private function currentCustomerId(): int
    {
        return (int)$this->session->getCustomerId();
    }
}
