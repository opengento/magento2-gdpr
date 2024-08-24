<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
use Opengento\Gdpr\Model\Config;

abstract class AbstractGuest extends AbstractAction
{
    public function __construct(
        RequestInterface $request,
        ResultFactory $resultFactory,
        ManagerInterface $messageManager,
        Config $config,
        protected OrderLoaderInterface $orderLoader,
        protected Registry $registry
    ) {
        parent::__construct($request, $resultFactory, $messageManager, $config);
    }

    public function execute(): ResultInterface|ResponseInterface
    {
        $result = $this->orderLoader->load($this->request);

        return $result instanceof ResultInterface ? $result : parent::execute();
    }

    protected function currentOrder(): OrderInterface
    {
        return $this->registry->registry('current_order');
    }
}
