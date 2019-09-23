<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\ViewModel\Privacy;

use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Opengento\Gdpr\Api\EraseEntityCheckerInterface;

final class EraseGuestDataProvider extends DataObject implements ArgumentInterface
{
    /**
     * @var EraseEntityCheckerInterface
     */
    private $eraseEntityChecker;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        EraseEntityCheckerInterface $eraseEntityChecker,
        Registry $registry,
        array $data = []
    ) {
        $this->eraseEntityChecker = $eraseEntityChecker;
        $this->registry = $registry;
        parent::__construct($data);
    }

    public function canCancel(): bool
    {
        if (!$this->hasData('can_cancel')) {
            $canCancel = $this->eraseEntityChecker->canCancel($this->currentOrderId(), 'order');
            $this->setData('can_cancel', $canCancel);
        }

        return (bool) $this->_getData('can_cancel');
    }

    public function canCreate(): bool
    {
        if (!$this->hasData('can_create')) {
            $canCreate = $this->eraseEntityChecker->canCreate($this->currentOrderId(), 'order');
            $this->setData('can_create', $canCreate);
        }

        return (bool) $this->_getData('can_create');
    }

    private function currentOrderId(): int
    {
        /** @var OrderInterface $order */
        $order = $this->registry->registry('current_order');

        return (int) $order->getEntityId();
    }
}
