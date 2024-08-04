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
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Controller\AbstractAction;
use Opengento\Gdpr\Model\Config;

class Export extends AbstractAction implements HttpGetActionInterface, AccountInterface
{
    public function __construct(
        RequestInterface $request,
        ResultFactory $resultFactory,
        ManagerInterface $messageManager,
        Config $config,
        private Session $customerSession,
        private ExportEntityManagementInterface $exportEntityManagement
    ) {
        parent::__construct($request, $resultFactory, $messageManager, $config);
    }

    protected function isAllowed(): bool
    {
        return $this->config->isExportEnabled();
    }

    protected function executeAction(): Redirect
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setRefererOrBaseUrl();

        try {
            $this->exportEntityManagement->create((int)$this->customerSession->getCustomerId(), 'customer');
            $this->messageManager->addSuccessMessage(new Phrase('You will be notified when the export is ready.'));
        } catch (AlreadyExistsException) {
            $this->messageManager->addNoticeMessage(new Phrase('A document is already available in your account.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('Something went wrong, please try again later!'));
        }

        return $resultRedirect;
    }
}
