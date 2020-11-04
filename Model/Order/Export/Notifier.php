<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Order\Export;

use Magento\Sales\Api\OrderRepositoryInterface;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Model\Export\NotifierInterface;
use Opengento\Gdpr\Model\Order\Notifier\SenderInterface;

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

    public function __construct(
        array $senders,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->senders = $senders;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @inheritdoc
     */
    public function notify(ExportEntityInterface $exportEntity): void
    {
        $order = $this->orderRepository->get($exportEntity->getEntityId());

        foreach ($this->senders as $sender) {
            $sender->send($order);
        }
    }
}
