<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Erase;

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
     * @var OrigDataRegistry
     */
    private $origDataRegistry;

    public function __construct(
        array $senders,
        OrigDataRegistry $origDataRegistry
    ) {
        $this->senders = $senders;
        $this->origDataRegistry = $origDataRegistry;
    }

    /**
     * @inheritdoc
     */
    public function notify(EraseEntityInterface $eraseEntity): void
    {
        $customer = $this->origDataRegistry->get($eraseEntity->getEntityId());

        foreach ($this->senders as $sender) {
            $sender->send($customer);
        }
    }
}
