<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service;

use Magento\Sales\Api\Data\OrderInterface;
use Opengento\Gdpr\Api\EraseGuestInterface;
use Opengento\Gdpr\Model\Config\Source\EraseComponents;

/**
 * Class EraseGuestManagement
 */
final class EraseGuestManagement implements EraseGuestInterface
{
    /**
     * @var \Opengento\Gdpr\Model\Config\Source\EraseComponents
     */
    private $eraseComponents;

    /**
     * @param \Opengento\Gdpr\Model\Config\Source\EraseComponents $eraseComponents
     */
    public function __construct(
        EraseComponents $eraseComponents
    ) {
        $this->eraseComponents = $eraseComponents;
    }

    /**
     * @inheritdoc
     */
    public function erase(OrderInterface $order): bool
    {
        foreach (\array_column($this->eraseComponents->toOptionArray(), 'value') as $component) {
            //todo process component
        }

        return true;
    }
}
