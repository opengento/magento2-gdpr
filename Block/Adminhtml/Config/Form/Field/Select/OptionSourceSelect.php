<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Block\Adminhtml\Config\Form\Field\Select;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

class OptionSourceSelect extends Select
{
    public function __construct(
        Context $context,
        private OptionSourceInterface $optionSource,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function setInputName(string $inputName): self
    {
        return $this->setData('name', $inputName);
    }

    protected function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->optionSource->toOptionArray());
        }

        return parent::_toHtml();
    }
}
