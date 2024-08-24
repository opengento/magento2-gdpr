<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Message\ManagerInterface;
use Opengento\Gdpr\Model\Config;

abstract class AbstractAction implements ActionInterface
{
    public function __construct(
        protected RequestInterface $request,
        protected ResultFactory $resultFactory,
        protected ManagerInterface $messageManager,
        protected Config $config
    ) {}

    public function execute()
    {
        return $this->isAllowed() ? $this->executeAction() : $this->forwardNoRoute();
    }

    /**
     * Execute action based on request and return result
     *
     * @throws NotFoundException
     */
    abstract protected function executeAction(): ResultInterface|ResponseInterface;

    protected function isAllowed(): bool
    {
        return $this->config->isModuleEnabled();
    }

    protected function forwardNoRoute(): ResultInterface
    {
        /** @var Forward $resultForward */
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);

        return $resultForward->forward('no_route');
    }
}
