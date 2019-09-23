<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\ViewModel\Privacy;

use Magento\Customer\Model\Session;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Opengento\Gdpr\Api\EraseEntityCheckerInterface;

final class EraseCustomerDataProvider extends DataObject implements ArgumentInterface
{
    /**
     * @var EraseEntityCheckerInterface
     */
    private $eraseEntityChecker;

    /**
     * @var Session
     */
    private $session;

    public function __construct(
        EraseEntityCheckerInterface $eraseEntityChecker,
        Session $session,
        array $data = []
    ) {
        $this->eraseEntityChecker = $eraseEntityChecker;
        $this->session = $session;
        parent::__construct($data);
    }

    public function canCancel(): bool
    {
        if (!$this->hasData('can_cancel')) {
            $canCancel = $this->eraseEntityChecker->canCancel($this->currentCustomerId(), 'customer');
            $this->setData('can_cancel', $canCancel);
        }

        return (bool) $this->_getData('can_cancel');
    }

    public function canCreate(): bool
    {
        if (!$this->hasData('can_create')) {
            $canCreate = $this->eraseEntityChecker->canCreate($this->currentCustomerId(), 'customer');
            $this->setData('can_create', $canCreate);
        }

        return (bool) $this->_getData('can_create');
    }

    private function currentCustomerId(): int
    {
        return (int) $this->session->getCustomerId();
    }
}
