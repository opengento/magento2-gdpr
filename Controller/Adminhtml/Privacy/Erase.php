<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Adminhtml\Privacy;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\EraseEntityManagementInterface;

/**
 * Class Erase
 */
class Erase extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Opengento_Gdpr::customer_erase';

    /**
     * @var \Opengento\Gdpr\Api\EraseEntityManagementInterface
     */
    private $eraseEntityManagement;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Opengento\Gdpr\Api\EraseEntityManagementInterface $eraseEntityManagement
     */
    public function __construct(
        Context $context,
        EraseEntityManagementInterface $eraseEntityManagement
    ) {
        $this->eraseEntityManagement = $eraseEntityManagement;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $this->eraseEntityManagement->process(
                $this->eraseEntityManagement->create((int) $this->getRequest()->getParam('id'), 'customer')
            );
            $this->messageManager->addSuccessMessage(new Phrase('You erased the customer.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('An error occurred on the server.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('customer/index');
    }
}
