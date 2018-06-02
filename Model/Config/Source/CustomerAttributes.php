<?php
/**
 * This file is part of the Flurrybox EnhancedPrivacy package.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Flurrybox EnhancedPrivacy
 * to newer versions in the future.
 *
 * @copyright Copyright (c) 2018 Flurrybox, Ltd. (https://flurrybox.com/)
 * @license   GNU General Public License ("GPL") v3.0
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flurrybox\EnhancedPrivacy\Model\Config\Source;

use \Magento\Framework\Data\OptionSourceInterface;

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
