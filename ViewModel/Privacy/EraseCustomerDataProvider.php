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
use Opengento\Gdpr\Api\EraseCustomerCheckerInterface;

/**
 * Class EraseCustomerDataProvider
 */
final class EraseCustomerDataProvider extends DataObject implements ArgumentInterface
{
    /**
     * @var \Opengento\Gdpr\Api\EraseCustomerCheckerInterface
     */
    private $eraseCustomerChecker;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $session;

    /**
     * @param \Opengento\Gdpr\Api\EraseCustomerCheckerInterface $eraseCustomerChecker
     * @param \Magento\Customer\Model\Session $session
     * @param array $data
     */
    public function __construct(
        EraseCustomerCheckerInterface $eraseCustomerChecker,
        Session $session,
        array $data = []
    ) {
        $this->eraseCustomerChecker = $eraseCustomerChecker;
        $this->session = $session;
        parent::__construct($data);
    }

    /**
     * Check if the erasure is already planned and could be canceled
     *
     * @return bool
     */
    public function canCancel(): bool
    {
        if (!$this->hasData('can_cancel')) {
            $this->setData('can_cancel', $this->eraseCustomerChecker->canCancel((int) $this->session->getCustomerId()));
        }

        return (bool) $this->_getData('can_cancel');
    }

    /**
     * Check if the erasure can be planned and processed
     *
     * @return bool
     */
    public function canCreate(): bool
    {
        if (!$this->hasData('can_create')) {
            $this->setData('can_create', $this->eraseCustomerChecker->canCreate((int) $this->session->getCustomerId()));
        }

        return (bool) $this->_getData('can_create');
    }
}
