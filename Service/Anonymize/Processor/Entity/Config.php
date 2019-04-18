<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Processor\Entity;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Config
 */
final class Config implements ConfigInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var string
     */
    private $configPath;

    /**
     * @var string
     */
    private $scopeType;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param string $configPath
     * @param string $scopeType
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        string $configPath,
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->configPath = $configPath;
        $this->scopeType = $scopeType;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes(?string $scopeCode = null): array
    {
        return \explode(',', $this->scopeConfig->getValue($this->configPath, $this->scopeType, $scopeCode) ?? '');
    }
}
