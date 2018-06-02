<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

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
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var \Opengento\Gdpr\Helper\Data
     */
    private $helper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Opengento\Gdpr\Helper\Data $helper
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
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        return ($this->helper->isModuleEnabled() && $this->helper->isPopupNotificationEnabled()) ? parent::_toHtml() : '';
    }

    /**
     * {@inheritdoc}
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
