<?php
/**
 * Copyright © OpenGento, All rights reserved.
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
final class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_init(EraseCustomer::class, EraseCustomerResourceModel::class);
        $this->_setIdFieldName(EraseCustomerInterface::ID);
    }
}
