<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const CONFIG_PATH_GENERAL_ENABLED = 'gdpr/general/enabled';
    private const CONFIG_PATH_ERASURE_ENABLED = 'gdpr/erasure/enabled';
    private const CONFIG_PATH_EXPORT_ENABLED = 'gdpr/export/enabled';

    public function __construct(private ScopeConfigInterface $scopeConfig) {}

    public function isModuleEnabled(int|string|null $website = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_GENERAL_ENABLED, ScopeInterface::SCOPE_WEBSITE, $website);
    }

    public function isErasureEnabled(int|string|null $website = null): bool
    {
        return $this->isModuleEnabled($website)
            && $this->scopeConfig->isSetFlag(self::CONFIG_PATH_ERASURE_ENABLED, ScopeInterface::SCOPE_WEBSITE, $website);
    }

    public function isExportEnabled(int|string|null $website = null): bool
    {
        return $this->isModuleEnabled($website)
            && $this->scopeConfig->isSetFlag(self::CONFIG_PATH_EXPORT_ENABLED, ScopeInterface::SCOPE_WEBSITE, $website);
    }
}
