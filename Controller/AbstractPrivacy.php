<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller;

use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Message\ManagerInterface;
use Opengento\Gdpr\Model\Config;

/**
 * This class is introduced to handle customer authentication verification.
 * We can't use the default AccountInterface or AccountPlugin
 * as they requires the action to inherit the default Magento AbstractAction
 * which is deprecated and which suffer of performance issues
 */
abstract class AbstractPrivacy extends AbstractAction
{
    /**
     * @var Session
     */
    protected Session $customerSession;

    /**
     * @var Http
     */
    private Http $response;

    public function __construct(
        RequestInterface $request,
        ResultFactory $resultFactory,
        ManagerInterface $messageManager,
        Config $config,
        Session $customerSession,
        Http $response
    ) {
        $this->customerSession = $customerSession;
        $this->response = $response;
        parent::__construct($request, $resultFactory, $messageManager, $config);
    }

    public function execute()
    {
        return $this->customerSession->authenticate() ? $this->defaultAction() : $this->response;
    }

    /**
     * @throws NotFoundException
     */
    private function defaultAction()
    {
        return $this->isAllowed() ? $this->executeAction() : $this->forwardNoRoute();
    }
}
