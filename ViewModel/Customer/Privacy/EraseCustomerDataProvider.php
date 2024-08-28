<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\ViewModel\Customer\Privacy;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Opengento\Gdpr\Api\EraseEntityCheckerInterface;

class EraseCustomerDataProvider implements ArgumentInterface
{
    private ?bool $canCancel = null;
    private ?bool $canCreate = null;

    public function __construct(
        private EraseEntityCheckerInterface $eraseEntityChecker,
        private Session $session
    ) {}

    public function canCancel(): bool
    {
        return $this->canCancel ??= $this->eraseEntityChecker->canCancel($this->currentCustomerId(), 'customer');
    }

    public function canCreate(): bool
    {
        return $this->canCreate ??= $this->eraseEntityChecker->canCreate($this->currentCustomerId(), 'customer');
    }

    private function currentCustomerId(): int
    {
        return (int)$this->session->getCustomerId();
    }
}
