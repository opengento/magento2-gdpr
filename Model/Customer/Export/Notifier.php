<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Export;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Model\Customer\Notifier\SenderInterface;
use Opengento\Gdpr\Model\Export\NotifierInterface;
use function array_values;

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
     * @param SenderInterface[] $senders
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        array $senders,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->senders = (static function (SenderInterface ...$senders): array {
            return $senders;
        })(...array_values($senders));
        $this->customerRepository = $customerRepository;
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function notify(ExportEntityInterface $exportEntity): void
    {
        $customer = $this->customerRepository->getById($exportEntity->getEntityId());

        foreach ($this->senders as $sender) {
            $sender->send($customer);
        }
    }
}
