<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Block\Messages;

use Magento\Cms\Helper\Page as HelperPage;
use Magento\Framework\Json\Encoder;
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
     * @var \Magento\Framework\Json\Encoder 
     */
    private $jsonEncoder;

    /**
     * @var string
     */
    protected $_template = 'Opengento_Gdpr::messages/popup.phtml';

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Opengento\Gdpr\Model\Config $config
     * @param \Magento\Cms\Helper\Page $helperPage
     * @param \Magento\Framework\Json\Encoder $jsonEncoder
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        HelperPage $helperPage,
        Encoder $jsonEncoder,
        array $data = []
    ) {
        $this->config = $config;
        $this->helperPage = $helperPage;
        $this->jsonEncoder = $jsonEncoder;
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

        return $this->jsonEncoder->encode($this->jsLayout);
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        return $this->config->isCookieDisclosureEnabled() ? parent::_toHtml() : '';
    }
}
