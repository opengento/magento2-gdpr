<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Adminhtml\Guest;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\EraseEntityManagementInterface;
use Opengento\Gdpr\Controller\Adminhtml\AbstractAction;
use Opengento\Gdpr\Model\Config;

/**
 * Class Erase
 */
class Erase extends AbstractAction implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Opengento_Gdpr::order_erase';

    /**
     * @var \Opengento\Gdpr\Api\EraseEntityManagementInterface
     */
    private $eraseEntityManagement;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Opengento\Gdpr\Model\Config $config
     * @param \Opengento\Gdpr\Api\EraseEntityManagementInterface $eraseEntityManagement
     */
    public function __construct(
        Context $context,
        Config $config,
        EraseEntityManagementInterface $eraseEntityManagement
    ) {
        $this->eraseEntityManagement = $eraseEntityManagement;
        parent::__construct($context, $config);
    }

    /**
     * @inheritdoc
     */
    protected function executeAction()
    {
        try {
            $this->eraseEntityManagement->process(
                $this->eraseEntityManagement->create((int) $this->getRequest()->getParam('id'), 'order')
            );
            $this->messageManager->addSuccessMessage(new Phrase('You erased the order.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('An error occurred on the server.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('sales/order/index');
    }
}
