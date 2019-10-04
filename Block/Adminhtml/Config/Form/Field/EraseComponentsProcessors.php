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

final class EraseComponentsProcessors extends AbstractFieldArray
{
    /**
     * @throws LocalizedException
     */
    public function getEraseComponentsSelectRenderer(): Select
    {
        if (!$this->hasData('erase_components_select_renderer')) {
            $this->setData(
                'erase_components_select_renderer',
                $this->getLayout()->createBlock(
                    $this->getData('erase_components_select'),
                    '',
                    ['data' => ['is_render_to_js_template' => true]]
                )
            );
        }

        return $this->getData('erase_components_select_renderer');
    }

    /**
     * @throws LocalizedException
     */
    public function getEraseProcessorsSelectRenderer(): Select
    {
        if (!$this->hasData('erase_processors_select_renderer')) {
            $this->setData(
                'erase_processors_select_renderer',
                $this->getLayout()->createBlock(
                    $this->getData('erase_processors_select'),
                    '',
                    ['data' => ['is_render_to_js_template' => true]]
                )
            );
        }

        return $this->getData('erase_processors_select_renderer');
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function _prepareToRender(): void
    {
        $this->addColumn(
            'component',
            [
                'label' => new Phrase('Component'),
                'renderer' => $this->getEraseComponentsSelectRenderer(),
            ]
        );
        $this->addColumn(
            'processor',
            [
                'label' => new Phrase('Processor'),
                'renderer' => $this->getEraseProcessorsSelectRenderer(),
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = (new Phrase('Add Component Erasure Strategy'))->render();
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $component = 'option_' . $this->getEraseComponentsSelectRenderer()->calcOptionHash($row->getData('component'));
        $processor = 'option_' . $this->getEraseProcessorsSelectRenderer()->calcOptionHash($row->getData('processor'));
        $row->setData('option_extra_attrs', [$component => 'selected="selected"', $processor => 'selected="selected"']);
    }
}
