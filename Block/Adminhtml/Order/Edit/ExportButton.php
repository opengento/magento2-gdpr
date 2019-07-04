<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Block\Adminhtml\Order\Edit;

use Magento\Backend\Block\AbstractBlock;
use Magento\Backend\Block\Context;
use Magento\Framework\Phrase;

/**
 * Class ExportButton
 */
final class ExportButton extends AbstractBlock
{
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        $this->_authorization = $context->getAuthorization();
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _construct(): void
    {
        parent::_construct();

        /** @var \Magento\Sales\Block\Adminhtml\Order\View $orderView */
        $orderView = $this->getLayout()->getBlock('sales_order_edit');
        $orderId = (int) $orderView->getOrderId();

        if ($this->_authorization->isAllowed('Opengento_Gdpr::order_export')) {
            $orderView->addButton(
                'order-view-export-button',
                [
                    'label' => new Phrase('Export Personal Data'),
                    'class' => 'export',
                    'onclick' => 'setLocation("' . $this->getUrl('sales/guest/export', ['id' => $orderId]) . '")',
                ]
            );
        }
    }
}
