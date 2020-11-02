<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Order\Erase;

use Magento\Sales\Api\OrderRepositoryInterface;
use Opengento\Gdpr\Api\Data\EraseEntityInterface;
use Opengento\Gdpr\Model\Erase\NotifierInterface;
use Opengento\Gdpr\Model\Order\Notifier\SenderInterface;
use function array_values;

final class Notifier implements NotifierInterface
{
    /**
     * @var SenderInterface[]
     */
    private $senders;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param SenderInterface[] $senders
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        array $senders,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->senders = (static function (SenderInterface ...$senders): array {
            return $senders;
        })(...array_values($senders));
        $this->orderRepository = $orderRepository;
    }

    public function notify(EraseEntityInterface $eraseEntity): void
    {
        $order = $this->orderRepository->get($eraseEntity->getEntityId());

        foreach ($this->senders as $sender) {
            $sender->send($order);
        }
    }
}
