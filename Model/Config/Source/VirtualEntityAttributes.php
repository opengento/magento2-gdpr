<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Model\EntitySnapshot\AttributeProviderInterface;

/**
 * Class VirtualEntityAttributes
 *
 * Use this virtual type only for registered entities in the metadata pool
 */
final class VirtualEntityAttributes implements OptionSourceInterface
{
    /**
     * @var \Magento\Framework\Model\EntitySnapshot\AttributeProviderInterface
     */
    private $attributeProvider;

    /**
     * @var string
     */
    private $entityType;

    /**
     * @var array
     */
    private $options;

    /**
     * @param \Magento\Framework\Model\EntitySnapshot\AttributeProviderInterface $attributeProvider
     * @param string $entityType
     */
    public function __construct(
        AttributeProviderInterface $attributeProvider,
        string $entityType
    ) {
        $this->attributeProvider = $attributeProvider;
        $this->entityType = $entityType;
        $this->options = [];
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        if (!$this->options) {
            foreach (\array_keys($this->attributeProvider->getAttributes($this->entityType)) as $attribute) {
                $this->options[] = ['value' => $attribute, 'label' => $attribute];
            }
        }

        return $this->options;
    }
}
