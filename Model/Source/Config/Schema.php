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

namespace Flurrybox\EnhancedPrivacy\Model\Source\Config;

use Magento\Framework\Option\ArrayInterface;

/**
 * Account delete schema types.
 */
class Schema implements ArrayInterface
{
    const DELETE = 0;
    const ANONYMIZE = 1;
    const DELETE_ANONYMIZE = 2;

    /**
     * Options getter.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::DELETE,
                'label' => __('Always delete')
            ],
            [
                'value' => self::ANONYMIZE,
                'label' => __('Always anonymize')
            ],
            [
                'value' => self::DELETE_ANONYMIZE,
                'label' => __('Delete if no orders made, anonymize otherwise')
            ]
        ];
    }

    /**
     * Get options in "key-value" format.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::DELETE => __('Always delete'),
            self::ANONYMIZE => __('Always anonymize'),
            self::DELETE_ANONYMIZE => __('Delete if no orders made, anonymize otherwise')
        ];
    }
}
