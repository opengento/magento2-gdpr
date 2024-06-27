<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Model\EntitySnapshot\AttributeProviderInterface;

use function array_keys;

/**
 * Class VirtualEntityAttributes
 *
 * Use this virtual type only for registered entities in the metadata pool
 */
class VirtualEntityAttributes implements OptionSourceInterface
{
    private ?array $options = null;

    public function __construct(
        private AttributeProviderInterface $attributeProvider,
        private string $entityType
    ) {}

    public function toOptionArray(): array
    {
        if ($this->options === null) {
            foreach (array_keys($this->attributeProvider->getAttributes($this->entityType)) as $attribute) {
                $this->options[] = ['value' => $attribute, 'label' => $attribute];
            }
        }

        return $this->options;
    }
}
