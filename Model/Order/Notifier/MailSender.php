<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Order\Notifier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\StoreManagerInterface;
use Opengento\Gdpr\Model\Notifier\AbstractMailSender;

class MailSender extends AbstractMailSender implements SenderInterface
{
    public function __construct(
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
     */
    public function send(OrderInterface $order): void
    {
        $storeId = $order->getStoreId() === null ? null : (int)$order->getStoreId();

        $this->sendMail(
            $order->getCustomerEmail(),
            $order->getCustomerName(),
            $storeId,
            [
                'order' => $order,
                'billing' => $order->getBillingAddress(),
                'store' => $this->storeManager->getStore($storeId),
                'customer_data' => [
                    'customer_name' => $order->getCustomerName(),
                ]
            ]
        );
    }
}
