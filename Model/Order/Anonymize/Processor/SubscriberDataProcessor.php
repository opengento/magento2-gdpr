<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Order\Anonymize\Processor;

use Magento\Newsletter\Model\ResourceModel\Subscriber as ResourceSubscriber;
use Magento\Sales\Api\OrderRepositoryInterface;
use Opengento\Gdpr\Model\Newsletter\SubscriberFactory;
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;

/**
 * Class SubscriberDataProcessor
 */
final class SubscriberDataProcessor implements ProcessorInterface
{
    /**
     * @var \Opengento\Gdpr\Service\Anonymize\AnonymizerInterface
     */
    private $anonymizer;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Opengento\Gdpr\Model\Newsletter\SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @var \Magento\Newsletter\Model\ResourceModel\Subscriber
     */
    private $subscriberResourceModel;

    /**
     * @param \Opengento\Gdpr\Service\Anonymize\AnonymizerInterface $anonymizer
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Opengento\Gdpr\Model\Newsletter\SubscriberFactory $subscriberFactory
     * @param \Magento\Newsletter\Model\ResourceModel\Subscriber $subscriberResourceModel
     */
    public function __construct(
        AnonymizerInterface $anonymizer,
        OrderRepositoryInterface $orderRepository,
        SubscriberFactory $subscriberFactory,
        ResourceSubscriber $subscriberResourceModel
    ) {
        $this->anonymizer = $anonymizer;
        $this->orderRepository = $orderRepository;
        $this->subscriberFactory = $subscriberFactory;
        $this->subscriberResourceModel = $subscriberResourceModel;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function execute(int $orderId): bool
    {
        $order = $this->orderRepository->get($orderId);

        /** @var \Opengento\Gdpr\Model\Newsletter\Subscriber $subscriber */
        $subscriber = $this->subscriberFactory->create();
        $subscriber->loadByEmail($order->getCustomerEmail());
        $this->anonymizer->anonymize($subscriber);
        $this->subscriberResourceModel->save($subscriber->getRealSubscriber());

        return true;
    }
}
