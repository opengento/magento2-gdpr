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
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\EraseEntityManagementInterface;
use Opengento\Gdpr\Api\EraseEntityRepositoryInterface;
use Opengento\Gdpr\Model\Config;

class Erase implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Opengento_Gdpr::customer_erase';

    public function __construct(
        private RequestInterface $request,
        private ManagerInterface $messageManager,
        private CustomerRepositoryInterface $customerRepository,
        private EraseEntityManagementInterface $eraseEntityManagement,
        private EraseEntityRepositoryInterface $eraseEntityRepository,
        private RedirectFactory $redirectFactory,
        private Config $config
    ) {}

    public function execute(): ResultInterface|ResponseInterface
    {
        try {
            $customerId = (int)$this->request->getParam('id');
            if ($this->config->isErasureEnabled($this->customerRepository->getById($customerId)->getWebsiteId())) {
                $this->eraseEntityManagement->process(
                    $this->eraseEntityRepository->getByEntity($customerId, 'customer')
                );
                $this->messageManager->addSuccessMessage(new Phrase('You erased the customer.'));
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('An error occurred on the server.'));
        }

        return $this->redirectFactory->create()->setPath('customer/index');
    }
}
