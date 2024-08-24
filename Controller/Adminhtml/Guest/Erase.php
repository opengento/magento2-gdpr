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
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Opengento\Gdpr\Api\Data\EraseEntityInterface;
use Opengento\Gdpr\Api\EraseEntityManagementInterface;
use Opengento\Gdpr\Api\EraseEntityRepositoryInterface;
use Opengento\Gdpr\Model\Config;

class Erase extends Action
{
    public const ADMIN_RESOURCE = 'Opengento_Gdpr::order_erase';

    public function __construct(
        Context $context,
        private StoreManagerInterface $storeManager,
        private OrderRepositoryInterface $orderRepository,
        private EraseEntityManagementInterface $eraseEntityManagement,
        private EraseEntityRepositoryInterface $eraseEntityRepository,
        private Config $config
    ) {
        parent::__construct($context);
    }

    public function execute(): ResultInterface|ResponseInterface
    {
        try {
            $orderId = (int)$this->getRequest()->getParam('id');
            if ($this->isOrderErasureEnabled($orderId)) {
                $this->eraseEntityManagement->process($this->fetchEntity($orderId));
                $this->messageManager->addSuccessMessage(new Phrase('You erased the order.'));
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('An error occurred on the server.'));
        }

        return $this->resultRedirectFactory->create()->setPath('sales/order/index');
    }

    /**
     * @throws NoSuchEntityException
     */
    private function isOrderErasureEnabled(int $orderId): bool
    {
        return $this->config->isErasureEnabled(
            $this->storeManager->getStore($this->orderRepository->get($orderId)->getStoreId())->getWebsiteId()
        );
    }

    /**
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    private function fetchEntity(int $orderId): EraseEntityInterface
    {
        try {
            return $this->eraseEntityRepository->getByEntity($orderId, 'order');
        } catch (NoSuchEntityException) {
            return $this->eraseEntityManagement->create($orderId, 'order');
        }
    }
}
