<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Config\Source;

use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Magento\Customer\Api\MetadataInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;

use function array_map;

class VirtualCustomerAttributes implements OptionSourceInterface
{
    private ?array $options = null;

    public function __construct(private MetadataInterface $metadata) {}

    public function toOptionArray(): array
    {
        return $this->options ??= array_map(
            static fn (AttributeMetadataInterface $attributeMetadata): array => [
                'value' => $attributeMetadata->getAttributeCode(),
                'label' => $attributeMetadata->getFrontendLabel(),
            ],
            $this->resolveAttributeMetadataList()
        );
    }

    private function resolveAttributeMetadataList(): array
    {
        try {
            return $this->metadata->getAllAttributesMetadata();
        } catch (LocalizedException) {
            return [];
        }
    }
}
