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

final class EraseDataProvider implements ArgumentInterface
{
    private EraseEntityCheckerInterface $eraseEntityChecker;

    private Registry $registry;

    /**
     * @var null|bool
     */
    private ?bool $canCancel;

    /**
     * @var null|bool
     */
    private ?bool $canCreate;

    public function __construct(
        EraseEntityCheckerInterface $eraseEntityChecker,
        Registry $registry
    ) {
        $this->eraseEntityChecker = $eraseEntityChecker;
        $this->registry = $registry;
    }

    public function canCancel(): bool
    {
        return $this->canCancel ??
            $this->canCancel = $this->eraseEntityChecker->canCancel($this->currentOrderId(), 'order');
    }

    public function canCreate(): bool
    {
        return $this->canCreate ??
            $this->canCreate = $this->eraseEntityChecker->canCreate($this->currentOrderId(), 'order');
    }

    private function currentOrderId(): int
    {
        /** @var OrderInterface $order */
        $order = $this->registry->registry('current_order');

        return (int) $order->getEntityId();
    }
}
