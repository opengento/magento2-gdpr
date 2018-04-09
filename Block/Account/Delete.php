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

namespace Flurrybox\EnhancedPrivacy\Block\Account;

use Flurrybox\EnhancedPrivacy\Block\Account;

/**
 * Customer delete account block.
 */
class Delete extends Account
{
    /**
     * Get action controller url.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->getUrl('privacy/delete/delete');
    }
}
