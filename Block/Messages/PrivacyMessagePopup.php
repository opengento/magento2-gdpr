<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Block\Messages;

use Magento\Cms\Helper\Page as HelperPage;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Opengento\Gdpr\Model\Config;

/**
 * Class PrivacyMessagePopup
 */
class PrivacyMessagePopup extends Template
{
    const COOKIE_NAME = 'cookies-policy';

    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Cms\Helper\Page
     */
    private $helperPage;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonSerializer;

    /**
     * @var string
     */
    protected $_template = 'Opengento_Gdpr::messages/popup.phtml';

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Opengento\Gdpr\Model\Config $config
     * @param \Magento\Cms\Helper\Page $helperPage
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        HelperPage $helperPage,
        Json $jsonSerializer,
        array $data = []
    ) {
        $this->config = $config;
        $this->helperPage = $helperPage;
        $this->jsonSerializer = $jsonSerializer;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getJsLayout()
    {
        $this->jsLayout['components']['enhanced-privacy-cookie-policy']['config'] = [
            'cookieName' => self::COOKIE_NAME,
            'learnMore' => $this->helperPage->getPageUrl($this->config->getPrivacyInformationPageId()),
            'notificationText' => $this->config->getCookieDisclosureInformationBlockId()
        ];

        return $this->jsonSerializer->serialize($this->jsLayout);
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        return $this->config->isCookieDisclosureEnabled() ? parent::_toHtml() : '';
    }
}
