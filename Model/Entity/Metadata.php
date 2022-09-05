<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity;

use Magento\Framework\App\Config\ScopeConfigInterface;
use function explode;

final class Metadata implements MetadataInterface
{
    private ScopeConfigInterface $scopeConfig;

    private string $configPath;

    private string $scopeType;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        string $configPath,
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->configPath = $configPath;
        $this->scopeType = $scopeType;
    }

    public function getAttributes(?string $scopeCode = null): array
    {
        return explode(',', $this->scopeConfig->getValue($this->configPath, $this->scopeType, $scopeCode) ?? '');
    }
}
