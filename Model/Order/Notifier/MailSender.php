<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Order\Notifier;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Opengento\Gdpr\Model\Notifier\AbstractMailSender;

final class MailSender extends AbstractMailSender implements SenderInterface
{
    /**
     * @inheritdoc
     * @param Order $order
     * @throws LocalizedException
     * @throws MailException
     */
    public function send(OrderInterface $order): void
    {
        $storeId = $order->getStoreId() === null ? null : (int) $order->getStoreId();
        $vars = [];//todo convert order as data array

        $this->sendMail($order->getCustomerEmail(), $order->getCustomerName(), $storeId, $vars);
    }
}
