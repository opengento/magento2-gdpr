<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Block\Adminhtml\Config\Form\Field\Select;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;
use Opengento\Gdpr\Model\Config\Source\EraseComponents as EraseComponentsSource;

final class EraseComponents extends Select
{
    /**
     * @var EraseComponentsSource
     */
    private $eraseComponentSource;

    public function __construct(
        Context $context,
        EraseComponentsSource $eraseComponentSource,
        array $data = []
    ) {
        $this->eraseComponentSource = $eraseComponentSource;
        parent::__construct($context, $data);
    }

    public function setInputName(string $inputName): self
    {
        return $this->setData('name', $inputName);
    }

    protected function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->eraseComponentSource->toOptionArray());
        }

        return parent::_toHtml();
    }
}
