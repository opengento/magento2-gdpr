<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Plugin\Controller;

use Opengento\Gdpr\Controller\AbstractPrivacy;
use Opengento\Gdpr\Model\Config;

/**
 * Class PrivacyActionPlugin
 */
final class PrivacyActionPlugin
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
     * @param callable $proceed
     * @param array ...$args
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\ResponseInterface
     */
    public function aroundExecute(AbstractPrivacy $subject, callable $proceed, ...$args)
    {
        return $this->config->isModuleEnabled() ? $proceed(...$args) : $subject->forwardNoRoute();
    }
}
