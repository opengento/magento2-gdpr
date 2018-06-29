<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Processor;

use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\ResourceModel\Subscriber as ResourceSubscriber;
use Opengento\Gdpr\Service\Anonymize\ProcessorInterface;
use Opengento\Gdpr\Service\Anonymize\AbstractAnonymize;

/**
 * Class SubscriberDataProcessor
 */
class SubscriberDataProcessor extends AbstractAnonymize implements ProcessorInterface
{
    /**
     * @var \Magento\Newsletter\Model\Subscriber
     */
    private $subscriber;

    /**
     * @var \Magento\Newsletter\Model\ResourceModel\Subscriber
     */
    private $subscriberResourceModel;

    /**
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     */
    public function __construct(
        Subscriber $subscriber,
        ResourceSubscriber $subscriberResourceModel
    ) {
        $this->subscriber = $subscriber;
        $this->subscriberResourceModel = $subscriberResourceModel;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(int $customerId): bool
    {
        try {
            $subscriber = $this->subscriber->loadByCustomerId($customerId);
            $subscriber->setData('subscriber_email', $this->anonymousEmail());
            $this->subscriberResourceModel->save($subscriber);

        } catch (NoSuchEntityException $e) {
            /** Silence is golden */
        }

        return true;
    }
}
