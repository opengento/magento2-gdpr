<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Guest;

use Exception;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Controller\AbstractGuest;
use Opengento\Gdpr\Model\Config;

class Export extends AbstractGuest implements HttpGetActionInterface
{
    public function __construct(
        RequestInterface $request,
        ResultFactory $resultFactory,
        ManagerInterface $messageManager,
        Config $config,
        OrderLoaderInterface $orderLoader,
        Registry $registry,
        private ExportEntityManagementInterface $exportEntityManagement
    ) {
        parent::__construct($request, $resultFactory, $messageManager, $config, $orderLoader, $registry);
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
            $this->exportEntityManagement->create((int)$this->currentOrder()->getEntityId(), 'order');
            $this->messageManager->addSuccessMessage(new Phrase('You will be notified when the export is ready.'));
        } catch (AlreadyExistsException) {
            $this->messageManager->addNoticeMessage(new Phrase('A document is already available in your order page.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('Something went wrong, please try again later!'));
        }

        return $resultRedirect;
    }
}
