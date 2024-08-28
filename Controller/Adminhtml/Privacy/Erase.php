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
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\Data\EraseEntityInterface;
use Opengento\Gdpr\Api\EraseEntityManagementInterface;
use Opengento\Gdpr\Api\EraseEntityRepositoryInterface;
use Opengento\Gdpr\Model\Config;

class Erase extends Action
{
    public const ADMIN_RESOURCE = 'Opengento_Gdpr::customer_erase';

    public function __construct(
        Context $context,
        private CustomerRepositoryInterface $customerRepository,
        private EraseEntityManagementInterface $eraseEntityManagement,
        private EraseEntityRepositoryInterface $eraseEntityRepository,
        private Config $config
    ) {
        parent::__construct($context);
    }

    public function execute(): ResultInterface|ResponseInterface
    {
        try {
            $customerId = (int)$this->getRequest()->getParam('id');
            if ($this->config->isErasureEnabled($this->customerRepository->getById($customerId)->getWebsiteId())) {
                $this->eraseEntityManagement->process($this->fetchEntity($customerId));
                $this->messageManager->addSuccessMessage(new Phrase('You erased the customer.'));
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('An error occurred on the server.'));
        }

        return $this->resultRedirectFactory->create()->setPath('customer/index');
    }

    /**
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    private function fetchEntity(int $customerId): EraseEntityInterface
    {
        try {
            return $this->eraseEntityRepository->getByEntity($customerId, 'customer');
        } catch (NoSuchEntityException) {
            return $this->eraseEntityManagement->create($customerId, 'customer');
        }
    }
}
