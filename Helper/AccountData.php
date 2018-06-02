<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

namespace Opengento\Gdpr\Helper;

use Opengento\Gdpr\Model\ResourceModel\CronSchedule\CollectionFactory as ScheduleCollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

/**
 * Helper to get account specific data.
 */
class AccountData extends AbstractHelper
{
    const ANONYMOUS_STR = 'Anonymous';
    const ANONYMOUS_DATE = 1;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var ScheduleCollectionFactory
     */
    protected $scheduleCollectionFactory;

    /**
     * AccountData constructor.
     *
     * @param Context $context
     * @param Session $customerSession
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param ScheduleCollectionFactory $scheduleCollectionFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        OrderCollectionFactory $orderCollectionFactory,
        ScheduleCollectionFactory $scheduleCollectionFactory
    ) {
        parent::__construct($context);

        $this->customerSession = $customerSession;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
    }

    /**
     * Check if customer has orders.
     *
     * @return bool
     */
    public function hasOrders()
    {
        if (!($customerId = $this->customerSession->getCustomerId())) {
            return false;
        }

        return (bool) $this->orderCollectionFactory->create($customerId)->getTotalCount();
    }

    /**
     * Check if customer is deleting his account.
     *
     * @return bool
     */
    public function isAccountToBeDeleted()
    {
        if (!($customerId = $this->customerSession->getCustomerId())) {
            return false;
        }

        if ($this->scheduleCollectionFactory->create()->getItemByColumnValue('customer_id', $customerId)) {
            return true;
        }

        return false;
    }
}
