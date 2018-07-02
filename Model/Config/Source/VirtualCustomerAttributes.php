<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Config\Source;

use Magento\Customer\Api\MetadataInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class VirtualCustomerAttributes
 */
class VirtualCustomerAttributes implements OptionSourceInterface
{
    /**
     * @var \Magento\Customer\Api\MetadataInterface
     */
    private $metadata;

    /**
     * @var array
     */
    private $options;

    /**
     * @param \Magento\Customer\Api\MetadataInterface $metadata
     * @param array $options
     */
    public function __construct(
        MetadataInterface $metadata,
        array $options = []
    ) {
        $this->metadata = $metadata;
        $this->options = $this->loadOptions($options);
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->options;
    }

    /**
     * Load an prepare customer address attributes options
     *
     * @param array $defaultOptions
     * @return array
     */
    public function loadOptions(array $defaultOptions = []): array
    {
        $options = [];

        try {
            $attributes = $this->metadata->getAllAttributesMetadata();
        } catch (LocalizedException $e) {
            $attributes = [];
        }

        foreach ($attributes as $attribute) {
            $options[] = ['value' => $attribute->getAttributeCode(), 'label' => $attribute->getFrontendLabel()];
        }

        return \array_merge($options, $defaultOptions);
    }
}
