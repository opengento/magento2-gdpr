<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Adminhtml\Privacy;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;
use Opengento\Gdpr\Model\Config;

class Export extends Action
{
    public const ADMIN_RESOURCE = 'Opengento_Gdpr::customer_export';

    public function __construct(
        Context $context,
        private CustomerRepositoryInterface $customerRepository,
        private ExportEntityManagementInterface $exportEntityManagement,
        private ExportEntityRepositoryInterface $exportEntityRepository,
        private Config $config,
        private FileFactory $fileFactory
    ) {
        parent::__construct($context);
    }

    public function execute(): ResultInterface|ResponseInterface
    {
        try {
            $customerId = (int)$this->getRequest()->getParam('id');
            if ($this->config->isExportEnabled($this->customerRepository->getById($customerId)->getWebsiteId())) {
                $exportEntity = $this->exportEntityManagement->export($this->fetchEntity($customerId));

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

        return $this->resultRedirectFactory->create()->setRefererOrBaseUrl();
    }

    /**
     * @throws AlreadyExistsException
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    private function fetchEntity(int $customerId): ExportEntityInterface
    {
        try {
            return $this->exportEntityRepository->getByEntity($customerId, 'customer');
        } catch (NoSuchEntityException) {
            return $this->exportEntityManagement->create($customerId, 'customer');
        }
    }
}
