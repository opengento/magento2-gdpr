<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Block\Adminhtml\Config\Form\Field\Select;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;
use Opengento\Gdpr\Service\Anonymize\AnonymizerPool;

/**
 * Class Anonymizers
 */
class Anonymizers extends Select
{
    /**
     * @var \Opengento\Gdpr\Service\Anonymize\AnonymizerPool
     */
    private $anonymizerPool;

    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Opengento\Gdpr\Service\Anonymize\AnonymizerPool $anonymizerPool
     * @param array $data
     */
    public function __construct(
        Context $context,
        AnonymizerPool $anonymizerPool,
        array $data = []
    ) {
        $this->anonymizerPool = $anonymizerPool;
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
            foreach (\array_keys($this->anonymizerPool->getAnonymizers()) as $anonymizer) {
                $this->addOption($anonymizer, $anonymizer);
            }
        }

        return parent::_toHtml();
    }
}
