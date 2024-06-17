<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;

use function array_column;
use function array_combine;
use function array_keys;

class Metadata implements MetadataInterface
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

    public function getAnonymizerStrategiesByAttributes(?string $scopeCode = null): array
    {
        $scope = $scopeCode ?? 'current_scope';

        if (!isset($this->cache[$scope])) {
            $metadata = $this->serializer->unserialize(
                $this->scopeConfig->getValue($this->configPath, $this->scopeType, $scopeCode) ?? '{}'
            );

            $this->cache[$scope] = array_combine(
                array_column($metadata, 'attribute'),
                array_column($metadata, 'anonymizer')
            );
        }

        return $this->cache[$scope];
    }

    public function getAttributes(?string $scopeCode = null): array
    {
        return array_keys($this->getAnonymizerStrategiesByAttributes($scopeCode));
    }
}
