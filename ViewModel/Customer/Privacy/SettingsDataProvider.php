<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\ViewModel\Customer\Privacy;

use Magento\Cms\Block\Block;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Store\Model\ScopeInterface;

final class SettingsDataProvider implements ArgumentInterface
{
    private const CONFIG_PATH_GENERAL_INFORMATION_BLOCK = 'gdpr/general/block_id';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var null|string
     */
    private $privacyInformationHtml;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        BlockFactory $blockFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->blockFactory = $blockFactory;
    }

    public function getPrivacyInformationHtml(): string
    {
        return $this->privacyInformationHtml ?? $this->privacyInformationHtml = $this->blockFactory->createBlock(
            Block::class,
            ['data' => ['block_id' => (string) $this->scopeConfig->getValue(
                self::CONFIG_PATH_GENERAL_INFORMATION_BLOCK,
                ScopeInterface::SCOPE_STORE
            )]]
        )->toHtml();
    }
}
