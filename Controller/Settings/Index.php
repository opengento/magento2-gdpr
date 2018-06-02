<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Settings;

use Opengento\Gdpr\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;

/**
 * Settings controller.
 */
class Index extends Action
{
    /**
     * @var \Opengento\Gdpr\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $session;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Opengento\Gdpr\Helper\Data $helper
     * @param \Magento\Customer\Model\Session $session
     */
    public function __construct(
        Context $context,
        Data $helper,
        Session $session
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->session->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        if (!$this->helper->isModuleEnabled()){
            $this->_forward('no_route');
        }

        return parent::dispatch($request);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        //todo refactor
        $this->_view->loadLayout();

        if ($block = $this->_view->getLayout()->getBlock('privacy_settings')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }

        $this->_view->getPage()->getConfig()->getTitle()->set(__('Privacy settings'));
        $this->_view->renderLayout();
    }
}
