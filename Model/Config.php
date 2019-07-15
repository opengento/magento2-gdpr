<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 */
final class Config
{
    /**#@+
     * Scope Config: Data Settings Paths
     */
    public const CONFIG_PATH_GENERAL_ENABLED = 'gdpr/general/enabled';
    public const CONFIG_PATH_GENERAL_INFORMATION_PAGE = 'gdpr/general/page_id';
    public const CONFIG_PATH_GENERAL_INFORMATION_BLOCK = 'gdpr/general/block_id';
    public const CONFIG_PATH_ERASURE_ENABLED = 'gdpr/erasure/enabled';
    public const CONFIG_PATH_ERASURE_DELAY = 'gdpr/erasure/delay';
    public const CONFIG_PATH_ERASURE_MAX_AGE = 'gdpr/erasure/entity_max_age';
    public const CONFIG_PATH_ERASURE_ALLOWED_STATES = 'gdpr/erasure/allowed_states';
    public const CONFIG_PATH_ERASURE_INFORMATION_BLOCK = 'gdpr/erasure/block_id';
    public const CONFIG_PATH_ERASURE_REMOVE_CUSTOMER = 'gdpr/erasure/remove_customer';
    public const CONFIG_PATH_ANONYMIZE_INFORMATION_BLOCK = 'gdpr/anonymize/block_id';
    public const CONFIG_PATH_EXPORT_ENABLED = 'gdpr/export/enabled';
    public const CONFIG_PATH_EXPORT_INFORMATION_BLOCK = 'gdpr/export/block_id';
    public const CONFIG_PATH_EXPORT_RENDERER = 'gdpr/export/renderer';
    public const CONFIG_PATH_COOKIE_DISCLOSURE_ENABLED = 'gdpr/cookie/enabled';
    public const CONFIG_PATH_COOKIE_INFORMATION_BLOCK = 'gdpr/cookie/block_id';
    /**#@-*/

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check if the current module is enabled
     *
     * @return bool
     */
    public function isModuleEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_GENERAL_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve the privacy information page ID
     *
     * @return string
     */
    public function getPrivacyInformationPageId(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_GENERAL_INFORMATION_PAGE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve the privacy information block ID
     *
     * @return string
     */
    public function getPrivacyInformationBlockId(): string
    {
        return (string) $this->scopeConfig->getValue(
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
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_ERASURE_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if the customer can be removed if he has no orders
     *
     * @return bool
     */
    public function isCustomerRemovedNoOrders(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_ERASURE_REMOVE_CUSTOMER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve the erasure delay in minutes before execution
     *
     * @return int
     */
    public function getErasureDelay(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::CONFIG_PATH_ERASURE_DELAY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve the maximum age for the entities before the erasure
     *
     * @return int
     */
    public function getErasureMaxAge(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::CONFIG_PATH_ERASURE_MAX_AGE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve the allowed order states to erase
     *
     * @return string[]
     */
    public function getAllowedStatesToErase(): array
    {
        return \explode(',', (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_ERASURE_ALLOWED_STATES,
            ScopeInterface::SCOPE_STORE
        ));
    }

    /**
     * Retrieve the erasure information block ID
     *
     * @return string
     */
    public function getErasureInformationBlockId(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_ERASURE_INFORMATION_BLOCK,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve the anonymize information block ID
     *
     * @return string
     */
    public function getAnonymizeInformationBlockId(): string
    {
        return (string) $this->scopeConfig->getValue(
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
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_EXPORT_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve the export information block ID
     *
     * @return string
     */
    public function getExportInformationBlockId(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_EXPORT_INFORMATION_BLOCK,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve the export renderer code
     *
     * @return string
     */
    public function getExportRendererCode(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_EXPORT_RENDERER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if the cookie disclosure is enabled
     *
     * @return bool
     */
    public function isCookieDisclosureEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_COOKIE_DISCLOSURE_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve the cookie disclosure information block ID
     *
     * @return string
     */
    public function getCookieDisclosureInformationBlockId(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_COOKIE_INFORMATION_BLOCK,
            ScopeInterface::SCOPE_STORE
        );
    }
}
