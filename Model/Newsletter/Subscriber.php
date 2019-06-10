<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Newsletter;

use Magento\Newsletter\Model\SubscriberFactory;

/**
 * `\Opengento\Gdpr\Model\Newsletter\Subscriber` class is the final state of `\Magento\Newsletter\Model\Subscriber`.
 */
final class Subscriber
{
    /**
     * @var \Magento\Newsletter\Model\Subscriber
     */
    private $subscriber;

    /**
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param array $data [optional]
     */
    public function __construct(
        SubscriberFactory $subscriberFactory,
        array $data = []
    ) {
        $this->subscriber = $subscriberFactory->create(['data' => $data]);
    }

    /**
     * @inheritdoc
     */
    public function __call($method, $args)
    {
        return $this->subscriber->{$method}(...$args);
    }
}
