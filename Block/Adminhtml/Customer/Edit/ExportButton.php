<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Block\Adminhtml\Customer\Edit;

use Magento\Customer\Block\Adminhtml\Edit\GenericButton;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class ExportButton
 */
final class ExportButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getButtonData(): array
    {
        $customerId = $this->getCustomerId();
        $buttonData = [];

        if ($customerId) {
            $buttonData = [
                'label' => new Phrase('Export Customer'),
                'class' => 'Export',
                'id' => 'customer-edit-export-button',
                'on_click' => 'deleteConfirm("' . new Phrase('Are you sure you want to do this?') . '", '
                    . '"' . $this->getUrl('customer/privacy/export', ['id' => $customerId]) . '", {"data": {}})',
                'sort_order' => 15,
                'aclResource' => 'Opengento_Gdpr::customer_export',
            ];
        }

        return $buttonData;
    }
}
