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
use Psr\Log\LoggerInterface;

final class MailSender extends AbstractMailSender implements SenderInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    private StoreManagerInterface $storeManager;

    public function __construct(
        LoggerInterface $logger,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        array $configPaths
    ) {
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        parent::__construct($transportBuilder, $scopeConfig, $configPaths);
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     * @throws MailException
     */
    public function send(OrderInterface $order): void
    {
        $storeId = $order->getStoreId() === null ? null : (int) $order->getStoreId();
        $vars = [
            'order' => $order,
            'billing' => $order->getBillingAddress(),
            'store' => $this->storeManager->getStore($order->getStoreId()),
            'customer_data' => [
                'customer_name' => $order->getCustomerName(),
            ],
        ];

        try {
            $this->sendMail($order->getCustomerEmail(), $order->getCustomerName(), $storeId, $vars);
            $this->logger->debug(__('GDPR Email Success'));
        } catch (MailException $exc) {
            $this->logger->error(__('GDPR Email Error: %1', $exc->getMessage()));
        }
        
    }
}
