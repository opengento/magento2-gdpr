<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

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
    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var OrderCollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @var ScheduleCollectionFactory
     */
    private $scheduleCollectionFactory;

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
    public function hasOrders(): bool
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
    public function isAccountToBeDeleted(): bool
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
