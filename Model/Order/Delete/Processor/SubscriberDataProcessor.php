<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Order\Delete\Processor;

use Exception;
use Magento\Newsletter\Model\ResourceModel\Subscriber as ResourceSubscriber;
use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;

final class SubscriberDataProcessor implements ProcessorInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @var ResourceSubscriber
     */
    private $subscriberResource;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SubscriberFactory $subscriberFactory,
        ResourceSubscriber $subscriberResource,
        StoreManagerInterface $storeManager
    ) {
        $this->orderRepository = $orderRepository;
        $this->subscriberFactory = $subscriberFactory;
        $this->subscriberResource = $subscriberResource;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(int $orderId): bool
    {
        $order = $this->orderRepository->get($orderId);

        /** @var Subscriber $subscriber */
        $subscriber = $this->subscriberFactory->create();
        $subscriber->loadBySubscriberEmail(
            $order->getCustomerEmail(),
            $this->storeManager->getStore($order->getStoreId())->getWebsiteId()
        );
        $this->subscriberResource->delete($subscriber);

        return true;
    }
}
