<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Delete\Processor;

use Exception;
use Magento\Newsletter\Model\ResourceModel\Subscriber as ResourceSubscriber;
use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\SubscriberFactory;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;

final class SubscriberDataProcessor implements ProcessorInterface
{
    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @var ResourceSubscriber
     */
    private $subscriberResourceModel;

    public function __construct(
        SubscriberFactory $subscriberFactory,
        ResourceSubscriber $subscriberResourceModel
    ) {
        $this->subscriberFactory = $subscriberFactory;
        $this->subscriberResourceModel = $subscriberResourceModel;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(int $customerId): bool
    {
        /** @var Subscriber $subscriber */
        $subscriber = $this->subscriberFactory->create();
        $subscriber->loadByCustomerId($customerId);
        $this->subscriberResourceModel->delete($subscriber);

        return true;
    }
}
