<?php
/**
 * This file is part of the Flurrybox EnhancedPrivacy package.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Flurrybox EnhancedPrivacy
 * to newer versions in the future.
 *
 * @copyright Copyright (c) 2018 Flurrybox, Ltd. (https://flurrybox.com/)
 * @license   GNU General Public License ("GPL") v3.0
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flurrybox\EnhancedPrivacy\Controller\Settings;

use Flurrybox\EnhancedPrivacy\Helper\Data;
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
     * @var Data
     */
    protected $helper;

    /**
     * @var Session
     */
    protected $session;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param Data $helper
     * @param Session $session
     */
    public function __construct(Context $context, Data $helper, Session $session)
    {
        parent::__construct($context);

        $this->helper = $helper;
        $this->session = $session;
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

        if (!$this->helper->isModuleEnabled()){
            $this->_forward('no_route');
        }

        return parent::dispatch($request);
    }

    /**
     * Execute controller.
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $this->_view->loadLayout();

        if ($block = $this->_view->getLayout()->getBlock('privacy_settings')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }

        $this->_view->getPage()->getConfig()->getTitle()->set(__('Privacy settings'));
        $this->_view->renderLayout();
    }
}
