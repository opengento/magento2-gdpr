<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Adminhtml\Guest;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Api\ExportEntityManagementInterface;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;
use Opengento\Gdpr\Model\Config;

class Export extends Action
{
    public const ADMIN_RESOURCE = 'Opengento_Gdpr::order_export';

    public function __construct(
        Context $context,
        private StoreManagerInterface $storeManager,
        private OrderRepositoryInterface $orderRepository,
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
            $orderId = (int)$this->getRequest()->getParam('id');
            if ($this->isOrderExportEnabled($orderId)) {
                $exportEntity = $this->exportEntityManagement->export($this->fetchEntity($orderId));

                return $this->fileFactory->create(
                    'guest_privacy_data_' . $exportEntity->getEntityId() . '.zip',
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
     * @throws NoSuchEntityException
     */
    private function isOrderExportEnabled(int $orderId): bool
    {
        return $this->config->isErasureEnabled(
            $this->storeManager->getStore($this->orderRepository->get($orderId)->getStoreId())->getWebsiteId()
        );
    }

    /**
     * @throws AlreadyExistsException
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    private function fetchEntity(int $orderId): ExportEntityInterface
    {
        try {
            return $this->exportEntityRepository->getByEntity($orderId, 'order');
        } catch (NoSuchEntityException) {
            return $this->exportEntityManagement->create($orderId, 'order');
        }
    }
}
