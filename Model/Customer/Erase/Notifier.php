<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Erase;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Opengento\Gdpr\Api\Data\EraseEntityInterface;
use Opengento\Gdpr\Model\Customer\Notifier\SenderInterface;
use Opengento\Gdpr\Model\Erase\NotifierInterface;

/**
 * Class Notifier
 */
final class Notifier implements NotifierInterface
{
    /**
     * @var \Opengento\Gdpr\Model\Customer\Notifier\SenderInterface[]
     */
    private $senders;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param \Opengento\Gdpr\Model\Customer\Notifier\SenderInterface[] $senders
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        array $senders,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->senders = (static function (SenderInterface ...$senders): array {
            return $senders;
        })(...\array_values($senders));
        $this->customerRepository = $customerRepository;
    }

    /**
     * @inheritdoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function notify(EraseEntityInterface $eraseEntity): void
    {
        $customer = $this->customerRepository->getById($eraseEntity->getEntityId());

        foreach ($this->senders as $sender) {
            $sender->send($customer);
        }
    }
}
