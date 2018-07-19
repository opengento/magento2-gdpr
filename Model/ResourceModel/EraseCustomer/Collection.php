<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\ResourceModel\EraseCustomer;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Opengento\Gdpr\Api\Data\EraseCustomerInterface;
use Opengento\Gdpr\Model\EraseCustomer;
use Opengento\Gdpr\Model\ResourceModel\EraseCustomer as EraseCustomerResourceModel;

/**
 * Erase Customer Scheduler Collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = EraseCustomerInterface::ID;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(EraseCustomer::class, EraseCustomerResourceModel::class);
    }
}
