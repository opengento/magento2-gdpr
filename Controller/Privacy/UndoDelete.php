<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller\Privacy;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Controller\AbstractPrivacy;
use Opengento\Gdpr\Helper\AccountData;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Opengento\Gdpr\Model\ResourceModel\CronSchedule\CollectionFactory;

/**
 * Action Undo Delete
 */
class UndoDelete extends AbstractPrivacy implements ActionInterface
{
    /**
     * @var \Opengento\Gdpr\Model\ResourceModel\CronSchedule\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $session;

    /**
     * @var \Opengento\Gdpr\Helper\AccountData
     */
    private $accountData;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Opengento\Gdpr\Model\ResourceModel\CronSchedule\CollectionFactory $collectionFactory
     * @param \Magento\Customer\Model\Session $session
     * @param \Opengento\Gdpr\Helper\AccountData $accountData
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        Session $session,
        AccountData $accountData
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->session = $session;
        $this->accountData = $accountData;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (!$this->accountData->isAccountToBeDeleted()) {
            return $this->forwardNoRoute();
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        try {
            /** @var \Opengento\Gdpr\Model\ResourceModel\CronSchedule\Collection $collection */
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('customer_id', $this->session->getCustomerId());
            $collection->walk('delete');

            $this->messageManager->addSuccessMessage(new Phrase('You canceled your account deletion.'));
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, new Phrase('Something went wrong, please try again later!'));
        }

        return $resultRedirect->setPath('privacy/settings');
    }
}
