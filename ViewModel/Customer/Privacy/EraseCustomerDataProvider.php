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
    private EraseEntityCheckerInterface $eraseEntityChecker;

    /**
     * @var Session
     */
    private Session $session;

    /**
     * @var null|bool
     */
    private ?bool $canCancel;

    /**
     * @var null|bool
     */
    private ?bool $canCreate;

    public function __construct(
        EraseEntityCheckerInterface $eraseEntityChecker,
        Session $session
    ) {
        $this->eraseEntityChecker = $eraseEntityChecker;
        $this->session = $session;
    }

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
