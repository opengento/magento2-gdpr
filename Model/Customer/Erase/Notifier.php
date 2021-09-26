<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Erase;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Opengento\Gdpr\Api\Data\EraseEntityInterface;
use Opengento\Gdpr\Model\Customer\Notifier\SenderInterface;
use Opengento\Gdpr\Model\Customer\OrigDataRegistry;
use Opengento\Gdpr\Model\Erase\NotifierInterface;

final class Notifier implements NotifierInterface
{
    /**
     * @var SenderInterface[]
     */
    private $senders;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var OrigDataRegistry
     */
    private $origDataRegistry;

    public function __construct(
        array $senders,
        CustomerRepositoryInterface $customerRepository,
        OrigDataRegistry $origDataRegistry
    ) {
        $this->senders = $senders;
        $this->customerRepository = $customerRepository;
        $this->origDataRegistry = $origDataRegistry;
    }

    /**
     * @ingeritdoc
     * @throws LocalizedException
     */
    public function notify(EraseEntityInterface $eraseEntity): void
    {
        $customerId = $eraseEntity->getEntityId();
        $customer = $this->origDataRegistry->get($customerId) ?? $this->customerRepository->getById($customerId);

        foreach ($this->senders as $sender) {
            $sender->send($customer);
        }
    }
}
