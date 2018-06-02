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

namespace Flurrybox\EnhancedPrivacy\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Module configuration helper.
 */
class Data extends AbstractHelper
{
    /**
     * Config XML paths
     */
    const CONFIG_ENABLE = 'customer/enhancedprivacy/general/enable';
    const CONFIG_INFORMATION_PAGE = 'customer/enhancedprivacy/general/information_page';
    const CONFIG_INFORMATION = 'customer/enhancedprivacy/general/information';
    const CONFIG_ACCOUNT_DELETION_ENABLED = 'customer/enhancedprivacy/account/account_deletion_enabled';
    const CONFIG_ACCOUNT_DELETION_SCHEMA = 'customer/enhancedprivacy/account/deletion_schema';
    const CONFIG_ACCOUNT_DELETION_TIME = 'customer/enhancedprivacy/account/deletion_time';
    const CONFIG_SUCCESS_MESSAGE = 'customer/enhancedprivacy/account/success_message';
    const CONFIG_ACCOUNT_DELETION_INFO = 'customer/enhancedprivacy/account/account_deletion_info';
    const CONFIG_ACCOUNT_DELETION_REASON_INFO = 'customer/enhancedprivacy/account/account_delete_reason_info';
    const CONFIG_ACCOUNT_ANONYMIZATION = 'customer/enhancedprivacy/account/account_anonymization_message_enabled';
    const CONFIG_ACCOUNT_ANONYMIZATION_MESSAGE = 'customer/enhancedprivacy/account/account_anonymization_message';
    const CONFIG_ACCOUNT_EXPORT_ENABLED = 'customer/enhancedprivacy/export/account_export_enabled';
    const CONFIG_ACCOUNT_EXPORT_INFORMATION = 'customer/enhancedprivacy/export/export_information';
    const CONFIG_ACCOUNT_POPUP_NOTIFICATION_ENABLED = 'customer/enhancedprivacy/cookie/popup_notification_enabled';
    const CONFIG_ACCOUNT_POPUP_TEXT = 'customer/enhancedprivacy/cookie/popup_text';
    const CONFIG_ACCOUNT_POPUP_LINK_TEXT = 'customer/enhancedprivacy/cookie/popup_link_text';
    const CONFIG_ACCOUNT_POPUP_TITLE = 'customer/enhancedprivacy/cookie/popup_title';
    const CONFIG_ACCOUNT_POPUP_BUTTON_AGREE_TEXT = 'customer/enhancedprivacy/cookie/popup_button_agree_text';

    /**
     * Schedule types
     */
    const SCHEDULE_TYPE_DELETE = 'delete';
    const SCHEDULE_TYPE_ANONYMIZE = 'anonymize';

    /**
     * Cookies names
     */
    const COOKIE_COOKIES_POLICY = 'cookies-policy';

    /**
     * Is Flurrybox EnhancedPrivacy module enabled
     *
     * @return bool
     */
    public function isModuleEnabled()
    {
        return (bool) $this->scopeConfig->getValue(self::CONFIG_ENABLE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get privacy information page url
     *
     * @return string|null
     */
    public function getInformationPage()
    {
        return $this->scopeConfig->getValue(self::CONFIG_INFORMATION_PAGE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get brief information about privacy
     *
     * @return string|null
     */
    public function getInformation()
    {
        return $this->scopeConfig->getValue(self::CONFIG_INFORMATION, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get success message
     *
     * @return string|null
     */
    public function getSuccessMessage()
    {
        return $this->scopeConfig->getValue(self::CONFIG_SUCCESS_MESSAGE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Is account deletion enabled
     *
     * @return bool
     */
    public function isAccountDeletionEnabled()
    {
        return (bool) $this->scopeConfig
            ->getValue(self::CONFIG_ACCOUNT_DELETION_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get account deletion schema type.
     *
     * @return int
     */
    public function getDeletionSchema()
    {
        return (int) $this->scopeConfig->getValue(self::CONFIG_ACCOUNT_DELETION_SCHEMA, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get account deletion schema type.
     *
     * @return int
     */
    public function getDeletionTime()
    {
        return (int) $this->scopeConfig->getValue(self::CONFIG_ACCOUNT_DELETION_TIME, ScopeInterface::SCOPE_STORE) * 60;
    }

    /**
     * Get account deletion information
     *
     * @return string|null
     */
    public function getAccountDeletionInfo()
    {
        return $this->scopeConfig->getValue(self::CONFIG_ACCOUNT_DELETION_INFO, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get information about deletion reason
     *
     * @return string|null
     */
    public function getDeletionReasonInfo()
    {
        return $this->scopeConfig->getValue(self::CONFIG_ACCOUNT_DELETION_REASON_INFO, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Is anonymization message enabled
     *
     * @return bool
     */
    public function isAnonymizationMessageEnabled()
    {
        return (bool) $this->scopeConfig
            ->getValue(self::CONFIG_ACCOUNT_ANONYMIZATION, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Returns anonymization message string
     *
     * @return string|null
     */
    public function getAnonymizationMessage()
    {
        return $this->scopeConfig->getValue(self::CONFIG_ACCOUNT_ANONYMIZATION_MESSAGE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Is account data export enabled
     *
     * @return bool
     */
    public function isAccountExportEnabled()
    {
        return (bool) $this->scopeConfig->getValue(self::CONFIG_ACCOUNT_EXPORT_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get information about export
     *
     * @return string|null
     */
    public function getAccountExportInformation()
    {
        return $this->scopeConfig->getValue(self::CONFIG_ACCOUNT_EXPORT_INFORMATION, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Is popup notification enabled
     *
     * @return bool
     */
    public function isPopupNotificationEnabled()
    {
        return (bool) $this->scopeConfig
            ->getValue(self::CONFIG_ACCOUNT_POPUP_NOTIFICATION_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get popup notification text
     *
     * @return string|null
     */
    public function getPopupNotificationText()
    {
        return $this->scopeConfig->getValue(self::CONFIG_ACCOUNT_POPUP_TEXT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get popup notification button text
     *
     * @return string|null
     */
    public function getPopupNotificationLinkText()
    {
        return $this->scopeConfig->getValue(self::CONFIG_ACCOUNT_POPUP_LINK_TEXT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get popup notification title
     *
     * @return string|null
     */
    public function getPopupNotificationTitle()
    {
        return $this->scopeConfig->getValue(self::CONFIG_ACCOUNT_POPUP_TITLE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get popup notification button agree text
     *
     * @return string|null
     */
    public function getPopupNotificationButtonAgreeText()
    {
        return $this->scopeConfig->getValue(self::CONFIG_ACCOUNT_POPUP_BUTTON_AGREE_TEXT, ScopeInterface::SCOPE_STORE);
    }
}