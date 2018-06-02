<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

namespace Opengento\Gdpr\Block\Messages;

use Opengento\Gdpr\Helper\Data;
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
