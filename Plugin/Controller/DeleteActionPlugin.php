<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Plugin\Controller;

use Magento\Framework\Controller\ResultInterface;
use Opengento\Gdpr\Controller\AbstractPrivacy;
use Opengento\Gdpr\Model\Config;

/**
 * Class DeleteActionPlugin
 */
class DeleteActionPlugin
{
    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @param \Opengento\Gdpr\Model\Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Check if the module is enabled for the current scope
     *
     * @param \Opengento\Gdpr\Controller\AbstractPrivacy $subject
     * @param \Magento\Framework\Controller\ResultInterface $result
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function beforeExecute(AbstractPrivacy $subject, ResultInterface $result): ResultInterface
    {
        return $this->config->isAccountDeletionEnabled() ? $result : $subject->forwardNoRoute();
    }
}
