<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\ViewModel\Customer\Privacy;

use Magento\Cms\Block\BlockByIdentifier;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Store\Model\ScopeInterface;

class SettingsDataProvider implements ArgumentInterface
{
    private const CONFIG_PATH_GENERAL_INFORMATION_BLOCK = 'gdpr/general/block_id';

    private ScopeConfigInterface $scopeConfig;

    private BlockFactory $blockFactory;

    private ?string $informationHtml;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        BlockFactory $blockFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->blockFactory = $blockFactory;
    }

    public function getPrivacyInformationHtml(): string
    {
        return $this->informationHtml ??= $this->blockFactory->createBlock(
            BlockByIdentifier::class,
            ['data' => ['identifier' => (string) $this->scopeConfig->getValue(
                self::CONFIG_PATH_GENERAL_INFORMATION_BLOCK,
                ScopeInterface::SCOPE_STORE
            )]]
        )->toHtml();
    }
}
