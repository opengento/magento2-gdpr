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
 * @todo split config
 */
final class Config
{
    /**
     * Scope Config: Data Settings Paths
     */
    public const CONFIG_PATH_GENERAL_ENABLED = 'gdpr/general/enabled';
    public const CONFIG_PATH_GENERAL_INFORMATION_PAGE = 'gdpr/general/page_id';
    public const CONFIG_PATH_GENERAL_INFORMATION_BLOCK = 'gdpr/general/block_id';
    public const CONFIG_PATH_ERASURE_ENABLED = 'gdpr/erasure/enabled';
    public const CONFIG_PATH_ERASURE_DELAY = 'gdpr/erasure/delay';
    public const CONFIG_PATH_ERASURE_MAX_AGE = 'gdpr/erasure/entity_max_age';
    public const CONFIG_PATH_ERASURE_SALES_MAX_AGE = 'gdpr/erasure/sales_max_age';
    public const CONFIG_PATH_ERASURE_ALLOWED_STATES = 'gdpr/erasure/allowed_states';
    public const CONFIG_PATH_ERASURE_INFORMATION_BLOCK = 'gdpr/erasure/block_id';
    public const CONFIG_PATH_ERASURE_REMOVE_CUSTOMER = 'gdpr/erasure/remove_customer';
    public const CONFIG_PATH_ANONYMIZE_INFORMATION_BLOCK = 'gdpr/anonymize/block_id';
    public const CONFIG_PATH_EXPORT_ENABLED = 'gdpr/export/enabled';
    public const CONFIG_PATH_EXPORT_FILE_NAME = 'gdpr/export/file_name';
    public const CONFIG_PATH_EXPORT_LIFE_TIME = 'gdpr/export/life_time';
    public const CONFIG_PATH_EXPORT_INFORMATION_BLOCK = 'gdpr/export/block_id';
    public const CONFIG_PATH_EXPORT_RENDERERS = 'gdpr/export/renderers';
    public const CONFIG_PATH_COOKIE_DISCLOSURE_ENABLED = 'gdpr/cookie/enabled';
    public const CONFIG_PATH_COOKIE_INFORMATION_BLOCK = 'gdpr/cookie/block_id';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function isModuleEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_GENERAL_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getPrivacyInformationPageId(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_GENERAL_INFORMATION_PAGE,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getPrivacyInformationBlockId(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_GENERAL_INFORMATION_BLOCK,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isErasureEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_ERASURE_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if the customer can be removed if he has no orders
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
     */
    public function getErasureMaxAge(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::CONFIG_PATH_ERASURE_MAX_AGE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve the maximum age for the sales information before the erasure
     */
    public function getErasureSalesMaxAge(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::CONFIG_PATH_ERASURE_SALES_MAX_AGE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string[]
     */
    public function getAllowedStatesToErase(): array
    {
        return \explode(',', (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_ERASURE_ALLOWED_STATES,
            ScopeInterface::SCOPE_STORE
        ));
    }

    public function getErasureInformationBlockId(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_ERASURE_INFORMATION_BLOCK,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getAnonymizeInformationBlockId(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_ANONYMIZE_INFORMATION_BLOCK,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isExportEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_EXPORT_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getExportFileName(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_EXPORT_FILE_NAME,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve the export file life time in minutes
     */
    public function getExportLifetime(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::CONFIG_PATH_EXPORT_LIFE_TIME,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getExportInformationBlockId(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_EXPORT_INFORMATION_BLOCK,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getExportRendererCodes(): array
    {
        return \explode(',', (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_EXPORT_RENDERERS,
            ScopeInterface::SCOPE_STORE
        ));
    }

    public function isCookieDisclosureEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_COOKIE_DISCLOSURE_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getCookieDisclosureInformationBlockId(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_COOKIE_INFORMATION_BLOCK,
            ScopeInterface::SCOPE_STORE
        );
    }
}
