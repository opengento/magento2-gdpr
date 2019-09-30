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

abstract class AbstractGuest extends AbstractAction
{
    /**
     * @var OrderLoaderInterface
     */
    protected $orderLoader;

    /**
     * @var Registry
     */
    protected $registry;

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

    public function execute()
    {
        if (($result = $this->orderLoader->load($this->getRequest())) instanceof ResultInterface) {
            return $result;
        }

        return parent::execute();
    }

    protected function currentOrder(): OrderInterface
    {
        return $this->registry->registry('current_order');
    }
}
