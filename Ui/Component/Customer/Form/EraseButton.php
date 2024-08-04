<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Ui\Component\Customer\Form;

use Magento\Backend\Block\Widget\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Block\Adminhtml\Edit\GenericButton;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Opengento\Gdpr\Api\EraseEntityCheckerInterface;
use Opengento\Gdpr\Model\Config;

class EraseButton extends GenericButton implements ButtonProviderInterface
{
    public function __construct(
        Context $context,
        Registry $registry,
        private AuthorizationInterface $authorization,
        private CustomerRepositoryInterface $customerRepository,
        private EraseEntityCheckerInterface $eraseCustomerChecker,
        private Config $config
    ) {
        parent::__construct($context, $registry);
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getButtonData(): array
    {
        $customerId = $this->getCustomerId();
        $customer = $this->customerRepository->getById($customerId);
        $buttonData = [];

        if ($customerId
            && $this->authorization->isAllowed('Opengento_Gdpr::customer_erase')
            && $this->config->isErasureEnabled($customer->getWebsiteId())
            && $this->eraseCustomerChecker->canCreate($customerId, 'customer')
        ) {
            $buttonData = [
                'label' => new Phrase('Erase Personal Data'),
                'class' => 'erase',
                'id' => 'opengento-gdpr-customer-edit-erase-button',
                'on_click' => 'deleteConfirm("' . new Phrase('Are you sure you want to do this?') . '", '
                    . '"' . $this->getUrl('customer/privacy/erase', ['id' => $customerId]) . '", {"data": {}})',
                'sort_order' => 15,
                'aclResource' => 'Opengento_Gdpr::customer_erase',
            ];
        }

        return $buttonData;
    }
}
