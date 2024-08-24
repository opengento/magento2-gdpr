<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Block\Adminhtml\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Html\Select;

class AttributesAnonymizers extends AbstractFieldArray
{
    private const ANONYMIZERS_SELECT = '\Opengento\Gdpr\Block\Adminhtml\Config\Form\Field\Select\Anonymizers';

    /**
     * @throws LocalizedException
     */
    public function getAnonymizersSelectRenderer(): Select
    {
        if (!$this->hasData('anonymizers_select_renderer')) {
            $this->setData(
                'anonymizers_select_renderer',
                $this->getLayout()->createBlock(
                    self::ANONYMIZERS_SELECT,
                    '',
                    ['data' => ['is_render_to_js_template' => true]]
                )
            );
        }

        return $this->_getData('anonymizers_select_renderer');
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
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
                'class' => 'required-entry',
                'renderer' => $this->getAnonymizersSelectRenderer(),
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = (new Phrase('Add Attribute\'s Anonymizer'))->render();
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $rowHash = $this->getAnonymizersSelectRenderer()->calcOptionHash($row->getData('anonymizer'));
        $row->setData('option_extra_attrs', ['option_' . $rowHash => 'selected="selected"']);
    }
}
