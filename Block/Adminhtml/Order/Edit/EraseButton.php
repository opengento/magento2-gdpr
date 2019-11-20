<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Block\Adminhtml\Order\Edit;

use Magento\Backend\Block\AbstractBlock;
use Magento\Backend\Block\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Sales\Block\Adminhtml\Order\View;
use Opengento\Gdpr\Api\EraseEntityCheckerInterface;

class EraseButton extends AbstractBlock
{
    /**
     * @var EraseEntityCheckerInterface
     */
    private $eraseEntityChecker;

    public function __construct(
        Context $context,
        EraseEntityCheckerInterface $eraseEntityChecker,
        array $data = []
    ) {
        /**
         * @todo remove this line
         * @link https://github.com/magento/magento2/pull/23576/
         */
        $this->_authorization = $context->getAuthorization();
        $this->eraseEntityChecker = $eraseEntityChecker;
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function _construct(): void
    {
        parent::_construct();

        /** @var View $orderView */
        $orderView = $this->getLayout()->getBlock('sales_order_edit');
        $orderId = (int) $orderView->getOrderId();

        if ($this->_authorization->isAllowed('Opengento_Gdpr::order_erase') &&
            $this->eraseEntityChecker->canCreate($orderId, 'order')
        ) {
            $confirmMessage = new Phrase('Are you sure you want to do this?');
            $eraseUrl = $this->getUrl('sales/guest/erase', ['id' => $orderId]);

            $orderView->addButton(
                'order-view-erase-button',
                [
                    'label' => new Phrase('Erase Personal Data'),
                    'class' => 'action-secondary erase',
                    'onclick' => 'deleteConfirm("' . $confirmMessage . '", "' . $eraseUrl . '", {"data":{}})',
                ],
                1
            );
        }
    }
}
