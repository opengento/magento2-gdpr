<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\ViewModel\Customer\Guest;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Opengento\Gdpr\Api\EraseEntityCheckerInterface;

class EraseDataProvider implements ArgumentInterface
{
    private ?bool $canCancel = null;
    private ?bool $canCreate = null;

    public function __construct(
        private EraseEntityCheckerInterface $eraseEntityChecker,
        private Registry $registry
    ) {}

    public function canCancel(): bool
    {
        return $this->canCancel ??= $this->eraseEntityChecker->canCancel($this->currentOrderId(), 'order');
    }

    public function canCreate(): bool
    {
        return $this->canCreate ??= $this->eraseEntityChecker->canCreate($this->currentOrderId(), 'order');
    }

    private function currentOrderId(): int
    {
        /** @var OrderInterface $order */
        $order = $this->registry->registry('current_order');

        return (int)$order->getEntityId();
    }
}
