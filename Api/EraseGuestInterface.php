<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * Interface EraseGuestInterface
 * @api
 */
interface EraseGuestInterface
{
    /**
     * Erase the guest related personal data
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return bool
     */
    public function erase(OrderInterface $order): bool;
}
