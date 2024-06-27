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

class Metadata implements MetadataInterface
{
    /**
     * @var string[][]
     */
    private array $cache;

    public function __construct(
        private ScopeConfigInterface $scopeConfig,
        private SerializerInterface $serializer,
        private string $configPath,
        private string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT
    ) {}

    public function getComponentsProcessors(?string $scopeCode = null): array
    {
        $scope = $scopeCode ?? 'current_scope';

        if (!isset($this->cache[$scope])) {
            $metadata = $this->scopeConfig->getValue($this->configPath, $this->scopeType, $scopeCode);
            if (!is_array($metadata)) {
                $metadata = $this->serializer->unserialize($metadata ?? '{}');
            }

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
        return $this->getComponentsProcessors($scopeCode)[$component]
            ?? throw new InvalidArgumentException(
                sprintf('There is no erasure processor registered for the component "%s".', $component)
            );
    }
}
