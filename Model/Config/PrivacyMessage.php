<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Config;

use Magento\Cms\Block\Block;
use Magento\Cms\Helper\Page as HelperPage;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Store\Model\ScopeInterface;

final class PrivacyMessage
{
    private const CONFIG_PATH_COOKIE_INFORMATION_BLOCK = 'gdpr/cookie/block_id';
    private const CONFIG_PATH_GENERAL_INFORMATION_PAGE = 'gdpr/general/page_id';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var HelperPage
     */
    private $helperPage;

    /**
     * @var string|null
     */
    private $blockHtml;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        BlockFactory $blockFactory,
        HelperPage $helperPage
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->blockFactory = $blockFactory;
        $this->helperPage = $helperPage;
    }

    public function getDisclosureInformationHtml(): string
    {
        return $this->blockHtml ?? $this->blockHtml = $this->createDisclosureInformationBlockHtml();
    }

    public function getLearnMoreUrl(): string
    {
        return $this->helperPage->getPageUrl((string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_GENERAL_INFORMATION_PAGE,
            ScopeInterface::SCOPE_STORE
        )) ?? '#';
    }

    private function createDisclosureInformationBlockHtml(): string
    {
        return $this->blockFactory->createBlock(
            Block::class,
            [
                'data' => [
                    'block_id' => (string) $this->scopeConfig->getValue(
                        self::CONFIG_PATH_COOKIE_INFORMATION_BLOCK,
                        ScopeInterface::SCOPE_STORE
                    ),
                ],
            ]
        )->toHtml();
    }
}
