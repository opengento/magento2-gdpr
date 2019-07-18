<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
use Opengento\Gdpr\Model\Config;

/**
 * Class AbstractGuest
 */
abstract class AbstractGuest extends AbstractAction
{
    /**
     * @var \Magento\Sales\Controller\AbstractController\OrderLoaderInterface
     */
    protected $orderLoader;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Opengento\Gdpr\Model\Config $config
     * @param \Magento\Sales\Controller\AbstractController\OrderLoaderInterface $orderLoader
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Context $context,
        Config $config,
        OrderLoaderInterface $orderLoader,
        Registry $registry
    ) {
        $this->orderLoader = $orderLoader;
        $this->registry = $registry;
        parent::__construct($context, $config);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        if (($result = $this->orderLoader->load($this->getRequest())) instanceof ResultInterface) {
            return $result;
        }
        if (!($this->registry->registry('current_order') instanceof OrderInterface)) {
            return $this->forwardNoRoute();
        }

        return parent::execute();
    }

    /**
     * Retrieve the current guest order ID
     *
     * @return int
     */
    protected function retrieveOrderId(): int
    {
        $order = $this->registry->registry('current_order');

        return $order && $order instanceof OrderInterface ? (int) $order->getEntityId() : -1;
    }
}
