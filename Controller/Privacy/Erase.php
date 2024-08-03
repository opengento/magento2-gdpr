<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Privacy;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\View\Result\Page;
use Opengento\Gdpr\Api\EraseEntityCheckerInterface;
use Opengento\Gdpr\Controller\AbstractPrivacy;
use Opengento\Gdpr\Model\Config;

class Erase extends AbstractPrivacy implements HttpGetActionInterface
{
    public function __construct(
        RequestInterface $request,
        ResultFactory $resultFactory,
        ManagerInterface $messageManager,
        Config $config,
        Session $customerSession,
        Http $response,
        private EraseEntityCheckerInterface $eraseCustomerChecker
    ) {
        parent::__construct($request, $resultFactory, $messageManager, $config, $customerSession, $response);
    }

    protected function isAllowed(): bool
    {
        return $this->config->isErasureEnabled();
    }

    protected function executeAction(): Page|Redirect
    {
        if ($this->eraseCustomerChecker->exists((int)$this->customerSession->getCustomerId(), 'customer')) {
            $this->messageManager->addErrorMessage(new Phrase('Your account is already being removed.'));
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setRefererOrBaseUrl();

            return $resultRedirect;
        }

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
