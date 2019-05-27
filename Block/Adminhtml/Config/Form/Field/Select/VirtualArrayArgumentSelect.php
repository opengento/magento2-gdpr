<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Block\Adminhtml\Config\Form\Field\Select;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;
use Opengento\Gdpr\Model\Config\Source\VirtualArrayArgumentList;

/**
 * Class VirtualArrayArgumentSelect
 */
class VirtualArrayArgumentSelect extends Select
{
    /**
     * @var \Opengento\Gdpr\Model\Config\Source\VirtualArrayArgumentList
     */
    private $arrayArgumentList;

    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Opengento\Gdpr\Model\Config\Source\VirtualArrayArgumentList $arrayArgumentList
     * @param array $data
     */
    public function __construct(
        Context $context,
        VirtualArrayArgumentList $arrayArgumentList,
        array $data = []
    ) {
        $this->arrayArgumentList = $arrayArgumentList;
        parent::__construct($context, $data);
    }

    /**
     * Set the input name
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value): self
    {
        return $this->setData('name', $value);
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->arrayArgumentList->toOptionArray());
        }

        return parent::_toHtml();
    }
}
