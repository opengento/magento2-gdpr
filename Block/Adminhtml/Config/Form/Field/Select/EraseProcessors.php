<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Block\Adminhtml\Config\Form\Field\Select;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;
use Opengento\Gdpr\Model\Config\Source\EraseProcessors as EraseProcessorsSource;

/**
 * Class EraseProcessors
 */
class EraseProcessors extends Select
{
    /**
     * @var \Opengento\Gdpr\Model\Config\Source\EraseProcessors
     */
    private $eraseProcessorSource;

    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Opengento\Gdpr\Model\Config\Source\EraseProcessors $eraseProcessorSource
     * @param array $data
     */
    public function __construct(
        Context $context,
        EraseProcessorsSource $eraseProcessorSource,
        array $data = []
    ) {
        $this->eraseProcessorSource = $eraseProcessorSource;
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
            $this->setOptions($this->eraseProcessorSource->toOptionArray());
        }

        return parent::_toHtml();
    }
}
