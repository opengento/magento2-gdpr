<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\ViewModel\Privacy;

use Magento\Cms\Block\Block;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\BlockFactory;
use Opengento\Gdpr\Model\Config;

final class SettingsDataProvider extends DataObject implements ArgumentInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var BlockFactory
     */
    private $blockFactory;

    public function __construct(
        Config $config,
        BlockFactory $blockFactory,
        array $data = []
    ) {
        $this->config = $config;
        $this->blockFactory = $blockFactory;
        parent::__construct($data);
    }

    public function isModuleEnabled(): bool
    {
        return $this->config->isModuleEnabled();
    }

    public function getPrivacyInformationHtml(): string
    {
        if (!$this->hasData('privacy_information')) {
            $block = $this->blockFactory->createBlock(
                Block::class,
                ['data' => ['block_id' => $this->config->getPrivacyInformationBlockId()]]
            );
            $this->setData('privacy_information', $block->toHtml());
        }

        return (string) $this->_getData('privacy_information');
    }
}
