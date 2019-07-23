<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Order\Notifier;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * Interface SenderInterface
 * @api
 */
interface SenderInterface
{
    /**
     * Send a notification to the guest
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return void
     */
    public function send(OrderInterface $order): void;
}
