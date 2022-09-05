<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Config;

use Magento\Cms\Block\BlockByIdentifier;
use Magento\Cms\Helper\Page as HelperPage;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Store\Model\ScopeInterface;

final class PrivacyMessage
{
    private const CONFIG_PATH_COOKIE_INFORMATION_ENABLED = 'gdpr/cookie/enabled';
    private const CONFIG_PATH_COOKIE_INFORMATION_BLOCK = 'gdpr/cookie/block_id';
    private const CONFIG_PATH_COOKIE_INFORMATION_PAGE = 'gdpr/cookie/page_id';

    private ScopeConfigInterface $scopeConfig;

    private BlockFactory $blockFactory;

    private HelperPage $helperPage;

    private ?string $blockHtml;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        BlockFactory $blockFactory,
        HelperPage $helperPage
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->blockFactory = $blockFactory;
        $this->helperPage = $helperPage;
    }

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_COOKIE_INFORMATION_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    public function getDisclosureInformationHtml(): string
    {
        return $this->blockHtml ??= $this->createDisclosureInformationBlockHtml();
    }

    public function getLearnMoreUrl(): ?string
    {
        return $this->helperPage->getPageUrl((string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_COOKIE_INFORMATION_PAGE,
            ScopeInterface::SCOPE_STORE
        ));
    }

    private function createDisclosureInformationBlockHtml(): string
    {
        return $this->blockFactory->createBlock(
            BlockByIdentifier::class,
            [
                'data' => [
                    'identifier' => (string) $this->scopeConfig->getValue(
                        self::CONFIG_PATH_COOKIE_INFORMATION_BLOCK,
                        ScopeInterface::SCOPE_STORE
                    ),
                ],
            ]
        )->toHtml();
    }
}
