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

final class Metadata implements MetadataInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var SerializerInterface
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
