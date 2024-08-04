<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Adminhtml\Guest;

use Exception;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Opengento\Gdpr\Api\EraseEntityManagementInterface;
use Opengento\Gdpr\Api\EraseEntityRepositoryInterface;
use Opengento\Gdpr\Model\Config;

class Erase implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Opengento_Gdpr::order_erase';

    public function __construct(
        private RequestInterface $request,
        private ManagerInterface $messageManager,
        private StoreManagerInterface $storeManager,
        private OrderRepositoryInterface $orderRepository,
        private EraseEntityManagementInterface $eraseEntityManagement,
        private EraseEntityRepositoryInterface $eraseEntityRepository,
        private Config $config,
        private RedirectFactory $redirectFactory,
    ) {}

    public function execute(): ResultInterface|ResponseInterface
    {
        try {
            $orderId = (int)$this->request->getParam('id');
            if ($this->isOrderErasureEnabled($orderId)) {
                $this->eraseEntityManagement->process(
                    $this->eraseEntityRepository->getByEntity($orderId, 'order')
                );
                $this->messageManager->addSuccessMessage(new Phrase('You erased the order.'));
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('An error occurred on the server.'));
        }

        return $this->redirectFactory->create()->setPath('sales/order/index');
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
}
