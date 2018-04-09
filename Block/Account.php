<?php
/**
 * This file is part of the Flurrybox EnhancedPrivacy package.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Flurrybox EnhancedPrivacy
 * to newer versions in the future.
 *
 * @copyright Copyright (c) 2018 Flurrybox, Ltd. (https://flurrybox.com/)
 * @license   GNU General Public License ("GPL") v3.0
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flurrybox\EnhancedPrivacy\Block;

use Flurrybox\EnhancedPrivacy\Helper\AccountData;
use Magento\Framework\View\Element\Template;
use Flurrybox\EnhancedPrivacy\Helper\Data;
use Magento\Sales\Model\Order\Config;

/**
 * Abstract account block implementation.
 */
abstract class Account extends Template
{
    /**
     * @var Data $helper
     */
    protected $helper;

    /**
     * @var Config
     */
    protected $orderConfig;

    /**
     * @var AccountData
     */
    protected $accountData;

    /**
     * Settings constructor.
     *
     * @param Template\Context $context
     * @param Data $helper
     * @param Config $orderConfig
     * @param AccountData $accountData
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
