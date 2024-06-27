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
    private ExportEntityCheckerInterface $exportEntityChecker;

    /**
     * @var Session
     */
    private Session $session;

    /**
     * @var null|bool
     */
    private ?bool $isExportEntityExists;

    /**
     * @var null|bool
     */
    private ?bool $isExported;

    public function __construct(
        ExportEntityCheckerInterface $exportEntityChecker,
        Session $session
    ) {
        $this->exportEntityChecker = $exportEntityChecker;
        $this->session = $session;
    }

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
