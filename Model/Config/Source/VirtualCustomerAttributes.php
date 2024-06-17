<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Config\Source;

use Magento\Customer\Api\MetadataInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;

class VirtualCustomerAttributes implements OptionSourceInterface
{
    private MetadataInterface $metadata;

    private array $options;

    public function __construct(
        MetadataInterface $metadata
    ) {
        $this->metadata = $metadata;
        $this->options = [];
    }

    public function toOptionArray(): array
    {
        if (!$this->options) {
            try {
                $attributes = $this->metadata->getAllAttributesMetadata();
            } catch (LocalizedException $e) {
                $attributes = [];
            }

            foreach ($attributes as $attribute) {
                $this->options[] = [
                    'value' => $attribute->getAttributeCode(),
                    'label' => $attribute->getFrontendLabel(),
                ];
            }
        }

        return $this->options;
    }
}
