<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Ui\Component\Order\Form;

use Magento\Backend\Block\AbstractBlock;
use Magento\Backend\Block\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Sales\Block\Adminhtml\Order\View;
use Opengento\Gdpr\Model\Config;

class ExportButton extends AbstractBlock
{
    public function __construct(
        Context $context,
        private Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @throws LocalizedException
     */
    protected function _construct(): void
    {
        parent::_construct();

        $orderView = $this->getLayout()->getBlock('sales_order_edit');
        if ($orderView instanceof View) {
            $orderId = (int)$orderView->getOrderId();

            if ($this->_authorization->isAllowed('Opengento_Gdpr::order_export')
                && $this->config->isErasureEnabled($orderView->getOrder()->getStore()->getWebsiteId())
            ) {
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
}
