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

class PrivacyMessagePopup extends Template
{
    public const COOKIE_NAME = 'cookies-policy';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var HelperPage
     */
    private $helperPage;

    /**
     * @var Json
     */
    private $jsonSerializer;

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

    public function getJsLayout(): string
    {
        $this->jsLayout['components']['enhanced-privacy-cookie-policy']['config'] = [
            'cookieName' => self::COOKIE_NAME,
            'learnMore' => $this->helperPage->getPageUrl($this->config->getPrivacyInformationPageId()),
            'notificationText' => $this->getCookieDisclosureInformationHtml(),
        ];

        return $this->jsonSerializer->serialize($this->jsLayout);
    }

    public function getCookieDisclosureInformationHtml(): string
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

    protected function _toHtml(): string
    {
        return $this->config->isCookieDisclosureEnabled() ? parent::_toHtml() : '';
    }
}
