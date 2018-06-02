<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Block;

use Opengento\Gdpr\Helper\AccountData;
use Magento\Framework\View\Element\Template;
use Opengento\Gdpr\Helper\Data;
use Magento\Sales\Model\Order\Config;

/**
 * Abstract account block implementation.
 */
abstract class Account extends Template
{
    /**
     * @var \Opengento\Gdpr\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    private $orderConfig;

    /**
     * @var \Opengento\Gdpr\Helper\AccountData
     */
    private $accountData;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Opengento\Gdpr\Helper\Data $helper
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Opengento\Gdpr\Helper\AccountData $accountData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helper,
        Config $orderConfig,
        AccountData $accountData,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->helper = $helper;
        $this->orderConfig = $orderConfig;
        $this->accountData = $accountData;
    }

    /**
     * Get action controller url
     *
     * @return string
     */
    public function getAction()
    {
        return $this->getUrl('privacy/delete/delete');
    }

    /**
     * Get privacy settings url
     *
     * @return string
     */
    public function getSettingsUrl()
    {
        return $this->getUrl('privacy/settings');
    }

    /**
     * Get Information Page url from configuration file
     *
     * @return string
     */
    public function getInformationPageUrl()
    {
        return $this->getUrl($this->helper->getInformationPage());
    }

    /**
     * Check if customer has orders.
     *
     * @return bool
     */
    public function hasOrders()
    {
        return $this->accountData->hasOrders();
    }

    /**
     * Check if customer is deleting his account.
     *
     * @return bool
     */
    public function isAccountToBeDeleted()
    {
        return $this->accountData->isAccountToBeDeleted();
    }
}
