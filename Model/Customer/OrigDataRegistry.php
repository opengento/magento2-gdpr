<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer;

use Magento\Customer\Api\Data\CustomerInterface;

final class OrigDataRegistry
{
    /**
     * @var CustomerInterface[]
     */
    private $customers = [];

    public function get(int $customerId): CustomerInterface
    {
        return $this->customers[$customerId];
    }

    public function set(CustomerInterface $customer): void
    {
        $this->customers[(int) $customer->getId()] = $customer;
    }
}
