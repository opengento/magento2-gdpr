<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Block\Messages;

use Magento\Cms\Block\Block;
use Magento\Cms\Helper\Page as HelperPage;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Opengento\Gdpr\Model\Config;

/**
 * Class PrivacyMessagePopup
 */
final class PrivacyMessagePopup extends Template
{
    public const COOKIE_NAME = 'cookies-policy';

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
     * @inheritdoc
     */
    public function getJsLayout(): string
    {
        $this->jsLayout['components']['enhanced-privacy-cookie-policy']['config'] = [
            'cookieName' => self::COOKIE_NAME,
            'learnMore' => $this->helperPage->getPageUrl($this->config->getPrivacyInformationPageId()),
            'notificationText' => $this->getCookieDisclosureInformation(),
        ];

        return $this->jsonSerializer->serialize($this->jsLayout);
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml(): string
    {
        return $this->config->isCookieDisclosureEnabled() ? parent::_toHtml() : '';
    }

    /**
     * Retrieve the cookie disclosure information html
     *
     * @return string
     */
    private function getCookieDisclosureInformation(): string
    {
        if (!$this->hasData('cookie_disclosure_information')) {
            try {
                $block = $this->getLayout()->createBlock(
                    Block::class,
                    'opengento.gdpr.cookie.disclosure.information',
                    ['data' => ['block_id' => $this->config->getCookieDisclosureInformationBlockId()]]
                );
                $this->setData('cookie_disclosure_information', $block->toHtml());
            } catch (LocalizedException $e) {
                $this->setData('cookie_disclosure_information', '');
            }
        }

        return (string) $this->_getData('cookie_disclosure_information');
    }
}
