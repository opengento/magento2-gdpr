<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Flurrybox\EnhancedPrivacy\Service\Export;

use Flurrybox\EnhancedPrivacy\Helper\Data;
use Magento\Newsletter\Model\Subscriber;

/**
 * Class SubscriberDataProcessor
 */
class SubscriberDataProcessor implements ProcessorInterface
{
    /**
     * @var \Magento\Newsletter\Model\Subscriber
     */
    private $subscriber;

    /**
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     */
    public function __construct(
        Subscriber $subscriber
    ) {
        $this->subscriber = $subscriber;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(string $customerEmail, array $data): array
    {
        $subscriber = $this->subscriber->loadByEmail($customerEmail);

        return array_merge_recursive(
            $data,
            ['orders' => $subscriber->toArray()]
        );
    }
}
