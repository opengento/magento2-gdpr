<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Adminhtml\Privacy;

use Exception;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;
use Opengento\Gdpr\Model\Config;

class Export implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Opengento_Gdpr::customer_export';

    public function __construct(
        private RequestInterface $request,
        private ManagerInterface $messageManager,
        private CustomerRepositoryInterface $customerRepository,
        private ExportEntityManagementInterface $exportEntityManagement,
        private ExportEntityRepositoryInterface $exportEntityRepository,
        private RedirectFactory $redirectFactory,
        private Config $config,
        private FileFactory $fileFactory
    ) {}

    public function execute(): ResultInterface|ResponseInterface
    {
        try {
            $customerId = (int)$this->request->getParam('id');
            if ($this->config->isExportEnabled($this->customerRepository->getById($customerId)->getWebsiteId())) {
                $exportEntity = $this->exportEntityManagement->export(
                    $this->exportEntityRepository->getByEntity($customerId, 'customer')
                );

                return $this->fileFactory->create(
                    'customer_privacy_data_' . $exportEntity->getEntityId() . '.zip',
                    [
                        'type' => 'filename',
                        'value' => $exportEntity->getFilePath(),
                    ],
                    DirectoryList::TMP
                );
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('An error occurred on the server.'));
        }

        return $this->redirectFactory->create()->setRefererOrBaseUrl();
    }
}
