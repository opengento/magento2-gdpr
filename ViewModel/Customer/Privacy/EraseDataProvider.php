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

class EraseDataProvider implements ArgumentInterface
{
    private const CONFIG_PATH_ERASURE_INFORMATION_BLOCK = 'gdpr/erasure/block_id';
    private const CONFIG_PATH_ANONYMIZE_INFORMATION_BLOCK = 'gdpr/anonymize/block_id';

    private ?string $erasureInformation = null;
    private ?string $anonymizeInformation = null;

    public function __construct(
        private ScopeConfigInterface $scopeConfig,
        private BlockFactory $blockFactory
    ) {}

    public function getErasureInformationHtml(): string
    {
        return $this->erasureInformation ??= $this->blockFactory->createBlock(
            BlockByIdentifier::class,
            ['data' => ['identifier' => (string)$this->scopeConfig->getValue(
                self::CONFIG_PATH_ERASURE_INFORMATION_BLOCK,
                ScopeInterface::SCOPE_STORE
            )]]
        )->toHtml();
    }

    public function getAnonymizeInformationHtml(): string
    {
        return $this->anonymizeInformation ??= $this->blockFactory->createBlock(
            BlockByIdentifier::class,
            ['data' => ['identifier' => (string)$this->scopeConfig->getValue(
                self::CONFIG_PATH_ANONYMIZE_INFORMATION_BLOCK,
                ScopeInterface::SCOPE_STORE
            )]]
        )->toHtml();
    }
}
