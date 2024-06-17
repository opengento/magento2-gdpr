<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

use function explode;

class Config
{
    /**
     * Scope Config: Data Settings Paths
     */
    public const CONFIG_PATH_GENERAL_ENABLED = 'gdpr/general/enabled';
    public const CONFIG_PATH_ERASURE_ENABLED = 'gdpr/erasure/enabled';
    public const CONFIG_PATH_ERASURE_ALLOWED_STATES = 'gdpr/erasure/allowed_states';
    public const CONFIG_PATH_EXPORT_ENABLED = 'gdpr/export/enabled';

    private ScopeConfigInterface $scopeConfig;

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

    public function isErasureEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_ERASURE_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string[]
     */
    public function getAllowedStatesToErase(): array
    {
        return explode(',', (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_ERASURE_ALLOWED_STATES,
            ScopeInterface::SCOPE_STORE
        ));
    }

    public function isExportEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_EXPORT_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }
}
