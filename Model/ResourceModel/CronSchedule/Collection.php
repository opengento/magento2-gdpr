<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

namespace Opengento\Gdpr\Model\ResourceModel\CronSchedule;

use Opengento\Gdpr\Model\CronSchedule;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Cron schedule collection.
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'schedule_id';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(CronSchedule::class, \Opengento\Gdpr\Model\ResourceModel\CronSchedule::class);
    }
}
