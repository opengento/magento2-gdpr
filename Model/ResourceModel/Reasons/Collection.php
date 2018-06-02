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

namespace Flurrybox\EnhancedPrivacy\Model\ResourceModel\Reasons;

use Flurrybox\EnhancedPrivacy\Model\Reasons;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Reasons collection.
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'reason_id';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Reasons::class, \Flurrybox\EnhancedPrivacy\Model\ResourceModel\Reasons::class);
    }
}
