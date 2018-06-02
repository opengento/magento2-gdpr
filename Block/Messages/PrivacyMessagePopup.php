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

namespace Flurrybox\EnhancedPrivacy\Block\Messages;

use Flurrybox\EnhancedPrivacy\Helper\Data;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\View\Element\Template;

/**
 * Privacy popup message block.
 */
class PrivacyMessagePopup extends Template
{
    /**
     * @var CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * PrivacyMessagePopup constructor.
     *
     * @param Template\Context $context
     * @param CookieManagerInterface $cookieManager
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CookieManagerInterface $cookieManager,
        Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->cookieManager = $cookieManager;
        $this->helper = $helper;
    }

    /**
     * Check if popup should be rendered before loading block.
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (
            !$this->helper->isModuleEnabled() ||
            !$this->helper->isPopupNotificationEnabled()
        ) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * Get JS layout configuration.
     *
     * @return string
     */
    public function getJsLayout()
    {
        $this->jsLayout['components']['enhanced-privacy-cookie-policy']['config'] = [
            'cookieName' => Data::COOKIE_COOKIES_POLICY,
            'learnMore' => $this->getUrl($this->helper->getInformationPage()),
            'notificationText' => $this->helper->getPopupNotificationText(),
            'notificationLinkText' => $this->helper->getPopupNotificationLinkText(),
            'notificationTitle' => $this->helper->getPopupNotificationTitle(),
            'notificationButtonAgreeText' => $this->helper->getPopupNotificationButtonAgreeText()
        ];

        return json_encode($this->jsLayout);
    }
}
