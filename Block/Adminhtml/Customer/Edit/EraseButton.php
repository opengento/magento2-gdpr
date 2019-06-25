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
use Opengento\Gdpr\Api\EraseEntityCheckerInterface;

/**
 * Class EraseButton
 */
final class EraseButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var \Opengento\Gdpr\Api\EraseEntityCheckerInterface
     */
    private $eraseCustomerChecker;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Opengento\Gdpr\Api\EraseEntityCheckerInterface $eraseCustomerChecker
     */
    public function __construct(
        Context $context,
        Registry $registry,
        EraseEntityCheckerInterface $eraseCustomerChecker
    ) {
        $this->eraseCustomerChecker = $eraseCustomerChecker;
        parent::__construct($context, $registry);
    }

    /**
     * @inheritdoc
     */
    public function getButtonData(): array
    {
        $customerId = $this->getCustomerId();
        $buttonData = [];

        if ($customerId && $this->eraseCustomerChecker->canCreate($customerId, 'customer')) {
            $buttonData = [
                'label' => new Phrase('Erase Customer'),
                'class' => 'erase',
                'id' => 'customer-edit-erase-button',
                'on_click' => 'deleteConfirm("' . new Phrase('Are you sure you want to do this?') . '", '
                    . '"' . $this->getUrl('customer/privacy/erase', ['id' => $customerId]) . '", {"data": {}})',
                'sort_order' => 15,
                'aclResource' => 'Opengento_Gdpr::customer_erase',
            ];
        }

        return $buttonData;
    }
}
