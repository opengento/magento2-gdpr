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

/**
 * Class OptionSourceSelect
 */
final class OptionSourceSelect extends Select
{
    /**
     * @var \Magento\Framework\Data\OptionSourceInterface
     */
    private $optionSource;

    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Data\OptionSourceInterface $optionSource
     * @param array $data
     */
    public function __construct(
        Context $context,
        OptionSourceInterface $optionSource,
        array $data = []
    ) {
        $this->optionSource = $optionSource;
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
            $this->setOptions($this->optionSource->toOptionArray());
        }

        return parent::_toHtml();
    }
}
