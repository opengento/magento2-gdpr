<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action\PerformedBy;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session;
use Opengento\Gdpr\Model\Action\PerformedByInterface;

final class Customer implements PerformedByInterface
{
    /**
     * @var Session
     */
    private Session $customerSession;

    private string $attributeName;

    public function __construct(
        Session $customerSession,
        string $attributeName = CustomerInterface::EMAIL
    ) {
        $this->customerSession = $customerSession;
        $this->attributeName = $attributeName;
    }

    public function get(): string
    {
        return $this->customerSession->isLoggedIn()
            ? $this->customerSession->getCustomer()->getData($this->attributeName)
            : '';
    }
}
