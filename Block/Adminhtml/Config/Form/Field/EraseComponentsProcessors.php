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
use Magento\Framework\View\Element\Html\Select;
use Opengento\Gdpr\Block\Adminhtml\Config\Form\Field\Select\EraseComponents;

/**
 * Class EraseComponentsProcessors
 */
class EraseComponentsProcessors extends AbstractFieldArray
{
    private const ERASE_PROCESSORS_SELECT = '\Opengento\Gdpr\Block\Adminhtml\Config\Form\Field\Select\EraseProcessors';

    /**
     * Retrieve the erase components select renderer
     *
     * @return \Opengento\Gdpr\Block\Adminhtml\Config\Form\Field\Select\EraseComponents
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getEraseComponentsSelectRenderer(): EraseComponents
    {
        if (!$this->hasData('erase_components_select_renderer')) {
            $this->setData(
                'erase_components_select_renderer',
                $this->getLayout()->createBlock(
                    EraseComponents::class,
                    '',
                    ['data' => ['is_render_to_js_template' => true]]
                )
            );
        }

        return $this->getData('erase_components_select_renderer');
    }

    /**
     * Retrieve the erase processors select renderer
     *
     * @return \Magento\Framework\View\Element\Html\Select
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getEraseProcessorsSelectRenderer(): Select
    {
        if (!$this->hasData('erase_processors_select_renderer')) {
            $this->setData(
                'erase_processors_select_renderer',
                $this->getLayout()->createBlock(
                    self::ERASE_PROCESSORS_SELECT,
                    '',
                    ['data' => ['is_render_to_js_template' => true]]
                )
            );
        }

        return $this->getData('erase_processors_select_renderer');
    }

    /**
     * @inheritdoc
     * @throws \Magento\Framework\Exception\LocalizedException
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
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $component = 'option_' . $this->getEraseComponentsSelectRenderer()->calcOptionHash($row->getData('component'));
        $processor = 'option_' . $this->getEraseProcessorsSelectRenderer()->calcOptionHash($row->getData('processor'));
        $row->setData('option_extra_attrs', [$component => 'selected="selected"', $processor => 'selected="selected"']);
    }
}
