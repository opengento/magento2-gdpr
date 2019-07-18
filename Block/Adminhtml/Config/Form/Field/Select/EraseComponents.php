<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Block\Adminhtml\Config\Form\Field\Select;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;
use Opengento\Gdpr\Model\Config\Source\EraseComponents as EraseComponentsSrouce;

/**
 * Class EraseComponents
 */
final class EraseComponents extends Select
{
    /**
     * @var \Opengento\Gdpr\Model\Config\Source\EraseComponents
     */
    private $eraseComponentSource;

    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Opengento\Gdpr\Model\Config\Source\EraseComponents $eraseComponentSource
     * @param array $data
     */
    public function __construct(
        Context $context,
        EraseComponentsSrouce $eraseComponentSource,
        array $data = []
    ) {
        $this->eraseComponentSource = $eraseComponentSource;
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
            $this->setOptions($this->eraseComponentSource->toOptionArray());
        }

        return parent::_toHtml();
    }
}
