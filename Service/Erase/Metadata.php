<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Erase;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class Metadata
 */
final class Metadata implements MetadataInterface
{
    private const CONFIG_PATH_ERASURE_COMPONENTS_PROCESSORS = 'gdpr/erasure/components_processors';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var string
     */
    private $configPath;

    /**
     * @var string
     */
    private $scopeType;

    /**
     * @var string[][]
     */
    private $cache;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param string $configPath
     * @param string $scopeType
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer,
        string $configPath = self::CONFIG_PATH_ERASURE_COMPONENTS_PROCESSORS,
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        $this->configPath = $configPath;
        $this->scopeType = $scopeType;
    }

    /**
     * @inheritdoc
     */
    public function getComponentsProcessors(?string $scopeCode = null): array
    {
        $scope = $scopeCode ?? 'current_scope';

        if (!isset($this->cache[$scope])) {
            $metadata = $this->serializer->unserialize(
                $this->scopeConfig->getValue($this->configPath, $this->scopeType, $scopeCode) ?? '{}'
            );

            $this->cache[$scope] = \array_combine(
                \array_column($metadata, 'component'),
                \array_column($metadata, 'processor')
            );
        }

        return $this->cache[$scope];
    }

    /**
     * @inheritdoc
     */
    public function getComponentProcessor(string $component, ?string $scopeCode = null): string
    {
        $componentsProcessors = $this->getComponentsProcessors($scopeCode);

        if (!isset($componentsProcessors[$component])) {
            throw new \InvalidArgumentException(
                \sprintf('There is no erasure processor registered for the component "%s".', $component)
            );
        }

        return $componentsProcessors[$component];
    }
}
