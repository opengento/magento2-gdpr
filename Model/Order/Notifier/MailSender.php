<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Order\Notifier;

use Magento\Sales\Api\Data\OrderInterface;
use Opengento\Gdpr\Model\Notifier\AbstractMailSender;

/**
 * Class MailSender
 */
final class MailSender extends AbstractMailSender implements SenderInterface
{
    /**
     * @inheritdoc
     * @param \Magento\Sales\Model\Order $order
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function send(OrderInterface $order): void
    {
        $storeId = $order->getStoreId() === null ? null : (int) $order->getStoreId();
        $vars = [];//todo convert order as data array

        $this->sendMail($order->getCustomerEmail(), $order->getCustomerName(), $storeId, $vars);
    }
}
