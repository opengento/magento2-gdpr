<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Erase;

use InvalidArgumentException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use function array_column;
use function array_combine;
use function sprintf;

final class Metadata implements MetadataInterface
{
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    private string $configPath;

    private string $scopeType;

    /**
     * @var string[][]
     */
    private array $cache;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer,
        string $configPath,
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        $this->configPath = $configPath;
        $this->scopeType = $scopeType;
    }

    public function getComponentsProcessors(?string $scopeCode = null): array
    {
        $scope = $scopeCode ?? 'current_scope';

        if (!isset($this->cache[$scope])) {
            $metadata = $this->serializer->unserialize(
                $this->scopeConfig->getValue($this->configPath, $this->scopeType, $scopeCode) ?? '{}'
            );

            $this->cache[$scope] = array_combine(
                array_column($metadata, 'component'),
                array_column($metadata, 'processor')
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
            throw new InvalidArgumentException(
                sprintf('There is no erasure processor registered for the component "%s".', $component)
            );
        }

        return $componentsProcessors[$component];
    }
}
