<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Block\Adminhtml\Customer\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Customer\Block\Adminhtml\Edit\GenericButton;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Opengento\Gdpr\Model\Config;

class ExportButton extends GenericButton implements ButtonProviderInterface
{
    private Config $config;

    public function __construct(
        Context $context,
        Registry $registry,
        Config $config
    ) {
        $this->config = $config;
        parent::__construct($context, $registry);
    }

    public function getButtonData(): array
    {
        $customerId = $this->getCustomerId();
        $buttonData = [];

        if ($customerId && $this->config->isModuleEnabled()) {
            $buttonData = [
                'label' => new Phrase('Export Personal Data'),
                'class' => 'Export',
                'id' => 'opengento-gdpr-customer-edit-export-button',
                'on_click' => 'deleteConfirm("' . new Phrase('Are you sure you want to do this?') . '", '
                    . '"' . $this->getUrl('customer/privacy/export', ['id' => $customerId]) . '", {"data": {}})',
                'sort_order' => 15,
                'aclResource' => 'Opengento_Gdpr::customer_export',
            ];
        }

        return $buttonData;
    }
}
