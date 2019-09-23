<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Customer\Export\Processor;

use Opengento\Gdpr\Model\Entity\DataCollectorInterface;
use Opengento\Gdpr\Model\Newsletter\Subscriber;
use Opengento\Gdpr\Model\Newsletter\SubscriberFactory;
use Opengento\Gdpr\Service\Export\Processor\AbstractDataProcessor;

final class SubscriberDataProcessor extends AbstractDataProcessor
{
    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    public function __construct(
        SubscriberFactory $subscriberFactory,
        DataCollectorInterface $dataCollector
    ) {
        $this->subscriberFactory = $subscriberFactory;
        parent::__construct($dataCollector);
    }

    public function execute(int $customerId, array $data): array
    {
        /** @var Subscriber $subscriber */
        $subscriber = $this->subscriberFactory->create();
        $subscriber->loadByCustomerId($customerId);
        $data['subscriber'] = $this->collectData($subscriber);

        return $data;
    }
}
