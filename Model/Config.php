<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 */
class Config
{
    /**#@+
     * Scope Config: Data Settings Paths
     */
    const CONFIG_PATH_GENERAL_ENABLED = 'gdpr/general/enabled';
    const CONFIG_PATH_GENERAL_INFORMATION_PAGE = 'gdpr/general/page_id';
    const CONFIG_PATH_GENERAL_INFORMATION_BLOCK = 'gdpr/general/block_id';
    const CONFIG_PATH_ERASURE_ENABLED = 'gdpr/erasure/enabled';
    const CONFIG_PATH_ERASURE_STRATEGY = 'gdpr/erasure/strategy';
    const CONFIG_PATH_ERASURE_INFORMATION_BLOCK = 'gdpr/erasure/block_id';
    const CONFIG_PATH_ANONYMIZE_INFORMATION_BLOCK = 'gdpr/anonymize/block_id';
    const CONFIG_PATH_EXPORT_ENABLED = 'gdpr/export/enabled';
    const CONFIG_PATH_EXPORT_INFORMATION_BLOCK = 'gdpr/export/block_id';
    /**#@-*/

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var string[]
     */
    private $processorConfigPaths;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param string[] $processorConfigPaths
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        array $processorConfigPaths = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->processorConfigPaths = $processorConfigPaths;
    }

    /**
     * Check if the current module is enabled
     *
     * @return bool
     */
    public function isModuleEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_GENERAL_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve the privacy information page ID
     *
     * @return int
     */
    public function getPrivacyInformationPageId(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::CONFIG_PATH_GENERAL_INFORMATION_PAGE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve the privacy information block ID
     *
     * @return int
     */
    public function getPrivacyInformationBlockId(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::CONFIG_PATH_GENERAL_INFORMATION_BLOCK,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if the erasure is enabled
     *
     * @return bool
     */
    public function isErasureEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_ERASURE_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check if a strategy setting exists for the processor
     *
     * @param string $processorName
     * @return bool
     */
    public function hasStrategySetting(string $processorName): bool
    {
        return isset($this->processorConfigPaths[$processorName]);
    }

    /**
     * Retrieve the strategy setting of the processor
     *
     * @param string $processorName
     * @return string
     */
    public function getStrategySetting(string $processorName): string
    {
        return $this->hasStrategySetting($processorName)
            ? $this->scopeConfig->getValue($this->processorConfigPaths[$processorName], ScopeInterface::SCOPE_STORE)
            : $this->getDefaultStrategy();
    }

    /**
     * Retrieve the default strategy to apply
     *
     * @return string
     */
    public function getDefaultStrategy(): string
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_ERASURE_STRATEGY, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve the erasure information block ID
     *
     * @return int
     */
    public function getErasureInformationBlockId(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::CONFIG_PATH_ERASURE_INFORMATION_BLOCK,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve the anonymize information block ID
     *
     * @return int
     */
    public function getAnonymizeInformationBlockId(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::CONFIG_PATH_ANONYMIZE_INFORMATION_BLOCK,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if the export is enabled
     *
     * @return bool
     */
    public function isExportEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_EXPORT_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve the export information block ID
     *
     * @return int
     */
    public function getExportInformationBlockId(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::CONFIG_PATH_EXPORT_INFORMATION_BLOCK,
            ScopeInterface::SCOPE_STORE
        );
    }









    /****************************************************************************************************************/

    /**#@+
     * Scope Config Data Paths
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
    const CONFIG_ACCOUNT_ANONYMIZATION_CUSTOMER_ATTRIBUTES = 'customer/enhancedprivacy/account/customer_attributes';
    const CONFIG_ACCOUNT_ANONYMIZATION_CUSTOMER_ADDRESS_ATTRIBUTES = 'customer/enhancedprivacy/account/customer_address_attributes';
    const CONFIG_ACCOUNT_EXPORT_ENABLED = 'customer/enhancedprivacy/export/account_export_enabled';
    const CONFIG_ACCOUNT_EXPORT_INFORMATION = 'customer/enhancedprivacy/export/export_information';
    const CONFIG_ACCOUNT_EXPORT_CUSTOMER_ATTRIBUTES = 'customer/enhancedprivacy/export/customer_attributes';
    const CONFIG_ACCOUNT_EXPORT_CUSTOMER_ADDRESS_ATTRIBUTES = 'customer/enhancedprivacy/export/customer_address_attributes';
    const CONFIG_ACCOUNT_POPUP_NOTIFICATION_ENABLED = 'customer/enhancedprivacy/cookie/popup_notification_enabled';
    const CONFIG_ACCOUNT_POPUP_TEXT = 'customer/enhancedprivacy/cookie/popup_text';
    const CONFIG_ACCOUNT_POPUP_LINK_TEXT = 'customer/enhancedprivacy/cookie/popup_link_text';
    const CONFIG_ACCOUNT_POPUP_TITLE = 'customer/enhancedprivacy/cookie/popup_title';
    const CONFIG_ACCOUNT_POPUP_BUTTON_AGREE_TEXT = 'customer/enhancedprivacy/cookie/popup_button_agree_text';
    /**#@-*/

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
     * Retrieve the privacy information page url
     *
     * @return string|null
     */
    public function getInformationPage(): ?string
    {
        return $this->scopeConfig->getValue(self::CONFIG_INFORMATION_PAGE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve brief information about privacy
     *
     * @return string|null
     */
    public function getInformation(): ?string
    {
        return $this->scopeConfig->getValue(self::CONFIG_INFORMATION, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve success message
     *
     * @return string|null
     */
    public function getSuccessMessage(): ?string
    {
        return $this->scopeConfig->getValue(self::CONFIG_SUCCESS_MESSAGE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check if the account deletion is enabled
     *
     * @return bool
     */
    public function isAccountDeletionEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_ACCOUNT_DELETION_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve the account deletion schema type
     *
     * @return int
     */
    public function getDeletionSchema(): int
    {
        return (int) $this->scopeConfig->getValue(self::CONFIG_ACCOUNT_DELETION_SCHEMA, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve the account deletion schema type
     *
     * @return int
     */
    public function getDeletionTime(): int
    {
        return (int) $this->scopeConfig->getValue(self::CONFIG_ACCOUNT_DELETION_TIME, ScopeInterface::SCOPE_STORE) * 60;
    }

    /**
     * Retrieve the account deletion information
     *
     * @return string|null
     */
    public function getAccountDeletionInfo(): ?string
    {
        return $this->scopeConfig->getValue(self::CONFIG_ACCOUNT_DELETION_INFO, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve the information about the deletion reason
     *
     * @return string|null
     */
    public function getDeletionReasonInfo(): ?string
    {
        return $this->scopeConfig->getValue(self::CONFIG_ACCOUNT_DELETION_REASON_INFO, ScopeInterface::SCOPE_STORE);
    }

    /**
     * CHeck if the anonymous message is enabled
     *
     * @return bool
     */
    public function isAnonymizationMessageEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_ACCOUNT_ANONYMIZATION, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve the anonymous message string
     *
     * @return string|null
     */
    public function getAnonymizationMessage(): ?string
    {
        return $this->scopeConfig->getValue(self::CONFIG_ACCOUNT_ANONYMIZATION_MESSAGE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve the anonymous customer attributes codes
     *
     * @return array
     */
    public function getAnonymizeCustomerAttributes(): array
    {
        return explode(',', $this->scopeConfig->getValue(self::CONFIG_ACCOUNT_ANONYMIZATION_CUSTOMER_ATTRIBUTES));
    }

    /**
     * Retrieve the anonymous customer address attributes codes
     *
     * @return array
     */
    public function getAnonymizeCustomerAddressAttributes(): array
    {
        return explode(',', $this->scopeConfig->getValue(self::CONFIG_ACCOUNT_ANONYMIZATION_CUSTOMER_ADDRESS_ATTRIBUTES));
    }

    /**
     * Check if the account data export is enabled
     *
     * @return bool
     */
    public function isAccountExportEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_ACCOUNT_EXPORT_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve the information about the export
     *
     * @return string|null
     */
    public function getAccountExportInformation(): ?string
    {
        return $this->scopeConfig->getValue(self::CONFIG_ACCOUNT_EXPORT_INFORMATION, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve the exported customer attributes codes
     *
     * @return array
     */
    public function getExportCustomerAttributes(): array
    {
        return explode(',', $this->scopeConfig->getValue(self::CONFIG_ACCOUNT_EXPORT_CUSTOMER_ATTRIBUTES));
    }

    /**
     * Retrieve the exported customer address attributes codes
     *
     * @return array
     */
    public function getExportCustomerAddressAttributes(): array
    {
        return explode(',', $this->scopeConfig->getValue(self::CONFIG_ACCOUNT_EXPORT_CUSTOMER_ADDRESS_ATTRIBUTES));
    }

    /**
     * Check if the popup notification is enabled
     *
     * @return bool
     */
    public function isPopupNotificationEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_ACCOUNT_POPUP_NOTIFICATION_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve the popup notification text
     *
     * @return string|null
     */
    public function getPopupNotificationText(): ?string
    {
        return $this->scopeConfig->getValue(self::CONFIG_ACCOUNT_POPUP_TEXT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve the popup notification button text
     *
     * @return string|null
     */
    public function getPopupNotificationLinkText(): ?string
    {
        return $this->scopeConfig->getValue(self::CONFIG_ACCOUNT_POPUP_LINK_TEXT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve the popup notification title
     *
     * @return string|null
     */
    public function getPopupNotificationTitle(): ?string
    {
        return $this->scopeConfig->getValue(self::CONFIG_ACCOUNT_POPUP_TITLE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve the popup notification button agree text
     *
     * @return string|null
     */
    public function getPopupNotificationButtonAgreeText(): ?string
    {
        return $this->scopeConfig->getValue(self::CONFIG_ACCOUNT_POPUP_BUTTON_AGREE_TEXT, ScopeInterface::SCOPE_STORE);
    }
}
