<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Newsletter;

use Magento\Newsletter\Model\Subscriber as SubscriberModel;
use Magento\Newsletter\Model\SubscriberFactory;

/**
 * `\Opengento\Gdpr\Model\Newsletter\Subscriber` class is the final state of `\Magento\Newsletter\Model\Subscriber`.
 *
 * @method SubscriberModel loadByCustomerId(int $customerId)
 * @method SubscriberModel loadByEmail(string $email)
 */
final class Subscriber
{
    private $subscriber;

    public function __construct(
        SubscriberFactory $subscriberFactory,
        array $data = []
    ) {
        $this->subscriber = $subscriberFactory->create(['data' => $data]);
    }

    public function getRealSubscriber(): SubscriberModel
    {
        return $this->subscriber;
    }

    public function __call($method, $args)
    {
        return $this->subscriber->{$method}(...$args);
    }
}
