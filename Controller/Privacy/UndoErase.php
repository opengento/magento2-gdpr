<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Privacy;

use Exception;
use Magento\Customer\Controller\AccountInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\EraseEntityManagementInterface;
use Opengento\Gdpr\Controller\AbstractAction;
use Opengento\Gdpr\Model\Config;

class UndoErase extends AbstractAction implements HttpPostActionInterface, AccountInterface
{
    public function __construct(
        RequestInterface $request,
        ResultFactory $resultFactory,
        ManagerInterface $messageManager,
        Config $config,
        private Session $customerSession,
        private EraseEntityManagementInterface $eraseEntityManagement
    ) {
        parent::__construct($request, $resultFactory, $messageManager, $config);
    }

    protected function isAllowed(): bool
    {
        return $this->config->isErasureEnabled();
    }

    protected function executeAction(): Redirect
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('customer/privacy/settings');

        try {
            $this->eraseEntityManagement->cancel((int)$this->customerSession->getCustomerId(), 'customer');
            $this->messageManager->addSuccessMessage(new Phrase('You canceled your account deletion.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('Something went wrong, please try again later!'));
        }

        return $resultRedirect;
    }
}
