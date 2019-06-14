<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Opengento\Gdpr\Model\Config;

abstract class AbstractAction extends Action
{
    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    protected $config;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Opengento\Gdpr\Model\Config $config
     */
    public function __construct(
        Context $context,
        Config $config
    ) {
        $this->config = $config;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
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
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    abstract protected function executeAction();

    /**
     * Check if the execution of the action is allowed
     *
     * @return bool
     */
    protected function isAllowed(): bool
    {
        return $this->config->isModuleEnabled();
    }

    /**
     * Create a result forward to 404
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    protected function forwardNoRoute(): ResultInterface
    {
        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);

        return $resultForward->forward('no_route');
    }
}
