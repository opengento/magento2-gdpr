<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Opengento\Gdpr\Model\Config;

abstract class AbstractAction extends Action
{
    /**
     * @var Config
     */
    protected $config;

    public function __construct(
        Context $context,
        Config $config
    ) {
        $this->config = $config;
        parent::__construct($context);
    }

    public function execute()
    {
        if ($this->isAllowed()) {
            return $this->executeAction();
        }

        return $this->forwardNoRoute();
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface
     * @throws NotFoundException
     */
    abstract protected function executeAction();

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
