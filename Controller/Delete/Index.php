<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

namespace Opengento\Gdpr\Controller\Delete;

use Opengento\Gdpr\Helper\AccountData;
use Opengento\Gdpr\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;

/**
 * Delete controller.
 */
class Index extends Action
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var AccountData
     */
    protected $accountData;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param Data $helper
     * @param Session $session
     * @param AccountData $accountData
     */
    public function __construct(Context $context, Data $helper, Session $session, AccountData $accountData)
    {
        parent::__construct($context);

        $this->helper = $helper;
        $this->session = $session;
        $this->accountData = $accountData;
    }

    /**
     * Dispatch controller.
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->session->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }

        if (
            !$this->helper->isModuleEnabled() ||
            !$this->helper->isAccountDeletionEnabled() ||
            $this->accountData->isAccountToBeDeleted()
        ) {
            $this->_forward('no_route');
        }

        return parent::dispatch($request);
    }

    /**
     * Execute controller.
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();

        if ($block = $this->_view->getLayout()->getBlock('privacy_delete')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }

        $this->_view->getPage()->getConfig()->getTitle()->set(__('Delete account'));

        $this->_view->renderLayout();
    }
}
