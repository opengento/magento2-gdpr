<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Config\Source;

use Magento\Cms\Model\ResourceModel\Block\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Block
 * @internal
 * @deprecated Removed for Magento 2.2.6 and Magento 2.3.0
 */
class Block implements OptionSourceInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $res = [];
            $existingIdentifiers = [];

            /** @var \Magento\Cms\Model\Block $item */
            foreach ($this->collectionFactory->create() as $item) {
                $identifier = $item->getData('identifier');

                $data['value'] = $identifier;
                $data['label'] = $item->getData('title');

                if (\in_array($identifier, $existingIdentifiers)) {
                    $data['value'] .= '|' . $item->getData('page_id');
                } else {
                    $existingIdentifiers[] = $identifier;
                }

                $res[] = $data;
            }

            $this->options = $res;
        }

        return $this->options;
    }
}
