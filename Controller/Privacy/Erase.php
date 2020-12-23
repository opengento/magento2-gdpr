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
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\EraseEntityCheckerInterface;
use Opengento\Gdpr\Controller\AbstractPrivacy;
use Opengento\Gdpr\Model\Config;

class Erase extends AbstractPrivacy implements HttpGetActionInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var EraseEntityCheckerInterface
     */
    private $eraseCustomerChecker;

    public function __construct(
        RequestInterface $request,
        ResultFactory $resultFactory,
        ManagerInterface $messageManager,
        Config $config,
        Session $session,
        EraseEntityCheckerInterface $eraseCustomerChecker
    ) {
        $this->session = $session;
        $this->eraseCustomerChecker = $eraseCustomerChecker;
        parent::__construct($request, $resultFactory, $messageManager, $config);
    }

    protected function isAllowed(): bool
    {
        return parent::isAllowed() && $this->config->isErasureEnabled();
    }

    protected function executeAction()
    {
        if ($this->eraseCustomerChecker->exists((int) $this->session->getCustomerId(), 'customer')) {
            $this->messageManager->addErrorMessage(new Phrase('Your account is already being removed.'));
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setRefererOrBaseUrl();

            return $resultRedirect;
        }

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
