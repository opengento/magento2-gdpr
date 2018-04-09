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
use Flurrybox\EnhancedPrivacy\Model\Source\Config\Schema;

/**
 * Customer privacy settings block.
 */
class Settings extends Account
{
    /**
     * Get delete page url.
     *
     * @return string
     */
    public function getDeletingPageUrl()
    {
        return $this->getUrl('privacy/delete');
    }

    /**
     * Get undo delete page url.
     *
     * @return string
     */
    public function getUndoDeletePageURL()
    {
        return $this->getUrl('privacy/delete/undodelete');
    }

    /**
     * Get Anonymise page url.
     */
    public function getAnonymisePageURL()
    {
        return $this->getUrl('privacy/anonymise');
    }

    /**
     * Get export controller.
     *
     * @return string
     */
    public function getExportAction()
    {
        return $this->getUrl('privacy/export/export');
    }

    /**
     * Get export page url.
     *
     * @return string
     */
    public function getExportPageUrl()
    {
        return $this->getUrl('privacy/export');
    }

    /**
     * Check if account should be anonymized instead of deleted.
     *
     * @return bool
     */
    public function shouldAnonymize()
    {
        return ($this->helper->getDeletionSchema() === Schema::ANONYMIZE || $this->hasOrders()) &&
            $this->helper->isAnonymizationMessageEnabled();
    }
}
