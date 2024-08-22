<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Notifier;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Helper\View;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Opengento\Gdpr\Model\Config\Entity\Erasure;
use Opengento\Gdpr\Model\Notifier\AbstractMailSender;

class MailSender extends AbstractMailSender implements SenderInterface
{
    public function __construct(
        private Erasure $erasureConfig,
        private View $customerViewHelper,
        private StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        array $configPaths
    ) {
        parent::__construct($transportBuilder, $scopeConfig, $configPaths);
    }

    /**
     * @throws LocalizedException
     * @throws MailException
     * @throws NoSuchEntityException
     */
    public function send(CustomerInterface $customer): void
    {
        $delay = $this->erasureConfig->getDelay();

        $storeId = $customer->getStoreId() === null ? null : (int)$customer->getStoreId();
        $vars = [
            'delay' => $delay !== 0 ? $delay / 60 : 0,
            'customer' => $customer,
            'store' => $this->storeManager->getStore($customer->getStoreId()),
            'customer_data' => [
                'customer_name' => $this->customerViewHelper->getCustomerName($customer),
            ],
        ];

        $this->sendMail($customer->getEmail(), $this->customerViewHelper->getCustomerName($customer), $storeId, $vars);
    }
}
