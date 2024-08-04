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
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;
use Opengento\Gdpr\Controller\AbstractAction;
use Opengento\Gdpr\Model\Config;

class Download extends AbstractAction implements HttpGetActionInterface, AccountInterface
{
    public function __construct(
        RequestInterface $request,
        ResultFactory $resultFactory,
        ManagerInterface $messageManager,
        Config $config,
        private Session $customerSession,
        private FileFactory $fileFactory,
        private ExportEntityRepositoryInterface $exportRepository
    ) {
        parent::__construct($request, $resultFactory, $messageManager, $config);
    }

    protected function isAllowed(): bool
    {
        return $this->config->isExportEnabled();
    }

    protected function executeAction(): ResultInterface|ResponseInterface
    {
        try {
            $customerId = (int)$this->customerSession->getCustomerId();

            return $this->fileFactory->create(
                'customer_privacy_data_' . $customerId . '.zip',
                [
                    'type' => 'filename',
                    'value' => $this->exportRepository->getByEntity($customerId, 'customer')->getFilePath(),
                ],
                DirectoryList::TMP
            );
        } catch (NoSuchEntityException) {
            $this->messageManager->addErrorMessage(
                new Phrase('The document does not exists and may have expired. Please renew your demand.')
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('Something went wrong, please try again later!'));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setRefererOrBaseUrl();
    }
}
