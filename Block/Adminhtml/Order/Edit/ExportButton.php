<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Block\Adminhtml\Order\Edit;

use Magento\Backend\Block\AbstractBlock;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Sales\Block\Adminhtml\Order\View;

class ExportButton extends AbstractBlock
{
    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function _construct(): void
    {
        parent::_construct();

        /** @var View $orderView */
        $orderView = $this->getLayout()->getBlock('sales_order_edit');
        $orderId = (int)$orderView->getOrderId();

        if ($this->_authorization->isAllowed('Opengento_Gdpr::order_export')) {
            $orderView->addButton(
                'opengento-gdpr-order-view-export-button',
                [
                    'label' => new Phrase('Export Personal Data'),
                    'class' => 'export',
                    'onclick' => 'setLocation("' . $this->getUrl('sales/guest/export', ['id' => $orderId]) . '")',
                ]
            );
        }
    }
}
