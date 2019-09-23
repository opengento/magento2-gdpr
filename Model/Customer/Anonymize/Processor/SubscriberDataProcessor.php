<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Anonymize\Processor;

use Exception;
use Magento\Newsletter\Model\ResourceModel\Subscriber as ResourceSubscriber;
use Opengento\Gdpr\Model\Newsletter\Subscriber;
use Opengento\Gdpr\Model\Newsletter\SubscriberFactory;
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;

final class SubscriberDataProcessor implements ProcessorInterface
{
    /**
     * @var AnonymizerInterface
     */
    private $anonymizer;

    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @var ResourceSubscriber
     */
    private $subscriberResourceModel;

    public function __construct(
        AnonymizerInterface $anonymizer,
        SubscriberFactory $subscriberFactory,
        ResourceSubscriber $subscriberResourceModel
    ) {
        $this->anonymizer = $anonymizer;
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
        $this->anonymizer->anonymize($subscriber);
        $this->subscriberResourceModel->save($subscriber->getRealSubscriber());

        return true;
    }
}
