<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Delete\Processor;

use Magento\Newsletter\Model\ResourceModel\Subscriber as ResourceSubscriber;
use Magento\Newsletter\Model\SubscriberFactory;
use Opengento\Gdpr\Service\Delete\ProcessorInterface;

/**
 * Class SubscriberDataProcessor
 */
final class SubscriberDataProcessor implements ProcessorInterface
{
    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @var \Magento\Newsletter\Model\ResourceModel\Subscriber
     */
    private $subscriberResourceModel;

    /**
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param \Magento\Newsletter\Model\ResourceModel\Subscriber $subscriberResourceModel
     */
    public function __construct(
        SubscriberFactory $subscriberFactory,
        ResourceSubscriber $subscriberResourceModel
    ) {
        $this->subscriberFactory = $subscriberFactory;
        $this->subscriberResourceModel = $subscriberResourceModel;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function execute(int $customerId): bool
    {
        /** @var \Magento\Newsletter\Model\Subscriber $subscriber */
        $subscriber = $this->subscriberFactory->create();
        $subscriber->loadByCustomerId($customerId);
        $this->subscriberResourceModel->delete($subscriber);

        return true;
    }
}
