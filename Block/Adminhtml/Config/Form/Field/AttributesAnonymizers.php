<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Block\Adminhtml\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Block\Adminhtml\Config\Form\Field\Select\Anonymizers;

/**
 * Class AttributesAnonymizers
 */
class AttributesAnonymizers extends AbstractFieldArray
{
    /**
     * Retrieve the anonymizers select renderer
     *
     * @return \Opengento\Gdpr\Block\Adminhtml\Config\Form\Field\Select\Anonymizers
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAnonymizersSelectRenderer(): Anonymizers
    {
        if (!$this->hasData('anonymizers_select_renderer')) {
            $this->setData(
                'anonymizers_select_renderer',
                $this->getLayout()->createBlock(
                    Anonymizers::class,
                    '',
                    ['data' => ['is_render_to_js_template' => true]]
                )
            );
        }

        return $this->getData('anonymizers_select_renderer');
    }

    /**
     * @inheritdoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareToRender(): void
    {
        $this->addColumn(
            'attribute',
            [
                'label' => new Phrase('Attribute Code'),
                'class' => 'required-entry',
            ]
        );
        $this->addColumn(
            'anonymizer',
            [
                'label' => new Phrase('Anonymizer'),
                'renderer' => $this->getAnonymizersSelectRenderer(),
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = (new Phrase('Add Storefront Default Qty'))->render();
    }

    /**
     * @inheritdoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $rowHash = $this->getAnonymizersSelectRenderer()->calcOptionHash($row->getData('anonymizer'));
        $row->setData('option_extra_attrs', ['option_' . $rowHash => 'selected="selected"']);
    }
}
