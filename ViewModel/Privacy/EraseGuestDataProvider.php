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

/**
 * Class EraseGuestDataProvider
 */
final class EraseGuestDataProvider extends DataObject implements ArgumentInterface
{
    /**
     * @var \Opengento\Gdpr\Api\EraseEntityCheckerInterface
     */
    private $eraseEntityChecker;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @param \Opengento\Gdpr\Api\EraseEntityCheckerInterface $eraseEntityChecker
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        EraseEntityCheckerInterface $eraseEntityChecker,
        Registry $registry,
        array $data = []
    ) {
        $this->eraseEntityChecker = $eraseEntityChecker;
        $this->registry = $registry;
        parent::__construct($data);
    }

    /**
     * Check if the erasure is already planned and could be canceled
     *
     * @return bool
     */
    public function canCancel(): bool
    {
        if (!$this->hasData('can_cancel')) {
            $canCancel = $this->eraseEntityChecker->canCancel((int) $this->resolveOrder()->getEntityId(), 'order');
            $this->setData('can_cancel', $canCancel);
        }

        return (bool) $this->_getData('can_cancel');
    }

    /**
     * Check if the erasure can be planned and processed
     *
     * @return bool
     */
    public function canCreate(): bool
    {
        if (!$this->hasData('can_create')) {
            $canCreate = $this->eraseEntityChecker->canCreate((int) $this->resolveOrder()->getEntityId(), 'order');
            $this->setData('can_create', $canCreate);
        }

        return (bool) $this->_getData('can_create');
    }

    /**
     * Resolve the current order
     *
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    private function resolveOrder(): OrderInterface
    {
        return $this->registry->registry('current_order');
    }
}
