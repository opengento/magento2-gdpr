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
use Opengento\Gdpr\Model\Config;

/**
 * Class EraseGuestDataProvider
 */
final class EraseGuestDataProvider extends DataObject implements ArgumentInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @param \Magento\Framework\Registry $registry
     * @param \Opengento\Gdpr\Model\Config $config
     * @param array $data
     */
    public function __construct(
        Registry $registry,
        Config $config,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->config = $config;
        parent::__construct($data);
    }

    /**
     * Check if the guest order can erase its personal data
     *
     * @return bool
     * @todo move code to dedicated service
     */
    public function canErase(): bool
    {
        if (!$this->hasData('can_erase')) {
            $order = $this->registry->registry('current_order');
            $canErase = $order &&
                $order instanceof OrderInterface &&
                \in_array($order->getState(), $this->config->getAllowedStatesToErase(), true);

            $this->setData('can_erase', $canErase);
        }

        return $this->_getData('can_erase');
    }
}
