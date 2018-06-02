<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

namespace Opengento\Gdpr\Controller\Delete;

use Exception;
use Opengento\Gdpr\Helper\AccountData;
use Opengento\Gdpr\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Opengento\Gdpr\Model\ResourceModel\CronSchedule\CollectionFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Undo customer delete controller.
 */
class UndoDelete extends Action
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var AccountData
     */
    protected $accountData;

    /**
     * UndoDelete constructor.
     *
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param Session $session
     * @param Data $helper
     * @param AccountData $accountData
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        Session $session,
        Data $helper,
        AccountData $accountData
    ) {
        parent::__construct($context);

        $this->collectionFactory = $collectionFactory;
        $this->session = $session;
        $this->helper = $helper;
        $this->accountData = $accountData;
    }

    /**
     * Dispatch controller.
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->session->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }

        if (
            !$this->helper->isModuleEnabled() ||
            !$this->helper->isAccountDeletionEnabled() ||
            !$this->accountData->isAccountToBeDeleted()
        ) {
            $this->_forward('no_route');
        }

        return parent::dispatch($request);
    }

    /**
     * Execute controller.
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            /** @var \Opengento\Gdpr\Model\CronSchedule $model */
            $model = $this->collectionFactory->create()
                ->getItemByColumnValue('customer_id', $this->session->getId());

            $model->getResource()->delete($model);

            $this->messageManager->addSuccessMessage(__('You canceled your account deletion.'));
        } catch (Exception $e) {
            $this->messageManager->addWarningMessage(__('Something went wrong, please try again later!'));
        }

        return $resultRedirect->setPath('privacy/settings');
    }
}
