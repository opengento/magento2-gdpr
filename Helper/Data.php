<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Helper;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Opengento\Gdpr\Model\ResourceModel\CronSchedule\CollectionFactory as ScheduleCollectionFactory;

/**
 * Helper to get account specific data
 */
class Data extends AbstractHelper
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Opengento\Gdpr\Model\ResourceModel\CronSchedule\CollectionFactory
     */
    private $scheduleCollectionFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Opengento\Gdpr\Model\ResourceModel\CronSchedule\CollectionFactory $scheduleCollectionFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        ScheduleCollectionFactory $scheduleCollectionFactory
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
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

        $scheduleCollection = $this->scheduleCollectionFactory->create()->addFieldToFilter('customer_id', $customerId);
        return (bool) $scheduleCollection->getSize();
    }
}
