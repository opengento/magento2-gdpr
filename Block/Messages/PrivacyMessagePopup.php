<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Block\Messages;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Opengento\Gdpr\Helper\Data;
use Opengento\Gdpr\Model\Config;

/**
 * Class PrivacyMessagePopup
 */
class PrivacyMessagePopup extends Template
{
    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Opengento\Gdpr\Model\Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        array $data = []
    ) {
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getJsLayout()
    {
        $this->jsLayout['components']['enhanced-privacy-cookie-policy']['config'] = [
            'cookieName' => Data::COOKIE_COOKIES_POLICY,
            'learnMore' => $this->getUrl($this->config->getInformationPage()),
            'notificationText' => $this->config->getPopupNotificationText(),
            'notificationLinkText' => $this->config->getPopupNotificationLinkText(),
            'notificationTitle' => $this->config->getPopupNotificationTitle(),
            'notificationButtonAgreeText' => $this->config->getPopupNotificationButtonAgreeText()
        ];

        return json_encode($this->jsLayout);
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        return ($this->config->isModuleEnabled() && $this->config->isPopupNotificationEnabled()) ? parent::_toHtml() : '';
    }
}
