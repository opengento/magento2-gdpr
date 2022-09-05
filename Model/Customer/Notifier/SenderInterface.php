<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Notifier;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * @api
 */
interface SenderInterface
{
    /**
     * @throws LocalizedException
     */
    public function send(CustomerInterface $customer): void;
}
