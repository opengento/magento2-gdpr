<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Order\Export\Processor;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Opengento\Gdpr\Model\Entity\DataCollectorInterface;
use Opengento\Gdpr\Model\Newsletter\Subscriber;
use Opengento\Gdpr\Model\Newsletter\SubscriberFactory;

class SubscriberDataProcessor extends AbstractDataProcessor
{
    private SubscriberFactory $subscriberFactory;

    public function __construct(
        SubscriberFactory $subscriberFactory,
        OrderRepositoryInterface $orderRepository,
        DataCollectorInterface $dataCollector
    ) {
        $this->subscriberFactory = $subscriberFactory;
        parent::__construct($orderRepository, $dataCollector);
    }

    protected function export(OrderInterface $order, array $data): array
    {
        /** @var Subscriber $subscriber */
        $subscriber = $this->subscriberFactory->create();
        $subscriber->loadByEmail($order->getCustomerEmail());
        $data['subscriber'] = $this->collectData($subscriber);

        return $data;
    }
}
