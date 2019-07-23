<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Notifier;

use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Interface SenderInterface
 * @api
 */
interface SenderInterface
{
    /**
     * Send a notification to the customer
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return void
     */
    public function send(CustomerInterface $customer): void;
}
