<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Config\Source;

use Magento\Customer\Model\AttributeMetadataDataProvider;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Customer attributes.
 */
class CustomerAttributes implements OptionSourceInterface
{
    /**
     * @var \Magento\Customer\Model\AttributeMetadataDataProvider
     */
    private $attributeMetadataDataProvider;

    /**
     * @param \Magento\Customer\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider
     */
    public function __construct(
        AttributeMetadataDataProvider $attributeMetadataDataProvider
    ) {
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $data = [];

        /** @var \Magento\Eav\Api\Data\AttributeInterface[] $attributes */
        $attributes = $this->attributeMetadataDataProvider->loadAttributesCollection(
            'customer',
            'adminhtml_customer'
        );

        foreach ($attributes as $attributeCode => $attribute) {
            $data[] = ['value' => $attributeCode, 'label' => $attribute->getFrontendLabel()];
        }

        return $data;
    }
}
