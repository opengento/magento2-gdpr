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
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\SessionException;
use Magento\Framework\Message\ManagerInterface;
use Opengento\Gdpr\Model\Config;

/**
 * This class is introduced to handle customer authentication verification.
 * We can't use the default AccountInterface or AccountPlugin
 * as they require the action to inherit the default Magento AbstractAction
 * which is deprecated and which suffer performance issues
 */
abstract class AbstractPrivacy extends AbstractAction
{
    public function __construct(
        RequestInterface $request,
        ResultFactory $resultFactory,
        ManagerInterface $messageManager,
        Config $config,
        protected Session $customerSession,
        protected Http $response
    ) {
        parent::__construct($request, $resultFactory, $messageManager, $config);
    }

    /**
     * @throws NotFoundException
     * @throws SessionException
     */
    final public function execute(): ResultInterface|ResponseInterface
    {
        return $this->customerSession->authenticate() ? $this->defaultAction() : $this->response;
    }

    /**
     * @throws NotFoundException
     */
    private function defaultAction(): ResultInterface|ResponseInterface
    {
        return $this->isAllowed() ? $this->executeAction() : $this->forwardNoRoute();
    }
}
