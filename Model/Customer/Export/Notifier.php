<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Export;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Model\Customer\Notifier\SenderInterface;
use Opengento\Gdpr\Model\Export\NotifierInterface;

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
    public function notify(ExportEntityInterface $exportEntity): void
    {
        $customer = $this->customerRepository->getById($exportEntity->getEntityId());

        foreach ($this->senders as $sender) {
            $sender->send($customer);
        }
    }
}
