<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Magento\Sales\Api\Data\OrderInterface;
use Opengento\Gdpr\Api\EraseGuestInterface;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;

/**
 * Class EraseGuestManagement
 */
final class EraseGuestManagement implements EraseGuestInterface
{
    /**
     * @var \Opengento\Gdpr\Service\Erase\ProcessorInterface
     */
    private $processor;

    /**
     * @param \Opengento\Gdpr\Service\Erase\ProcessorInterface $processor
     */
    public function __construct(
        ProcessorInterface $processor
    ) {
        $this->processor = $processor;
    }

    /**
     * @inheritdoc
     */
    public function erase(OrderInterface $order): bool
    {
        return $this->processor->execute((int) $order->getEntityId());
    }
}
