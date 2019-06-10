<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Newsletter;

use Magento\Newsletter\Model\Subscriber as SubscriberModel;

/**
 * `\Opengento\Gdpr\Model\Newsletter\Subscriber` class is the final state of `\Magento\Newsletter\Model\Subscriber`.
 */
final class Subscriber extends SubscriberModel
{
    /**
     * @var \Magento\Newsletter\Model\Subscriber
     */
    private $subscriber;

    /**
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     */
    public function __construct(
        SubscriberModel $subscriber
    ) {
        $this->subscriber = $subscriber;
    }

    /**
     * @inheritdoc
     */
    public function __call($method, $args)
    {
        return $this->subscriber->{$method}(...$args);
    }
}
