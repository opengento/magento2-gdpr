<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

namespace Opengento\Gdpr\Model\Config\Source;

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
    public function __construct(\Magento\Customer\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider) {
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
    }

    /**
     * Returns an array of all options of the series attribute
     *
     * @return array|null
     */
    public function toOptionArray()
    {
        $data = array();

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
