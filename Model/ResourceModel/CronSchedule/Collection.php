<?php
/**
 * This file is part of the Flurrybox EnhancedPrivacy package.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Flurrybox EnhancedPrivacy
 * to newer versions in the future.
 *
 * @copyright Copyright (c) 2018 Flurrybox, Ltd. (https://flurrybox.com/)
 * @license   GNU General Public License ("GPL") v3.0
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flurrybox\EnhancedPrivacy\Model\ResourceModel\CronSchedule;

use Flurrybox\EnhancedPrivacy\Model\CronSchedule;
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
        $this->_init(CronSchedule::class, \Flurrybox\EnhancedPrivacy\Model\ResourceModel\CronSchedule::class);
    }
}
