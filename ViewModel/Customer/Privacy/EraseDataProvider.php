<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\ViewModel\Customer\Privacy;

use Magento\Cms\Block\Block;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\BlockFactory;
use Opengento\Gdpr\Model\Config;

final class EraseDataProvider implements ArgumentInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var null|string
     */
    private $erasureInformation;

    /**
     * @var null|string
     */
    private $anonymizeInformation;

    public function __construct(
        Config $config,
        BlockFactory $blockFactory
    ) {
        $this->config = $config;
        $this->blockFactory = $blockFactory;
    }

    public function getErasureInformationHtml(): string
    {
        return $this->erasureInformation ??
            $this->erasureInformation = $this->blockFactory->createBlock(
                Block::class,
                ['data' => ['block_id' => $this->config->getErasureInformationBlockId()]]
            )->toHtml();
    }

    public function getAnonymizeInformationHtml(): string
    {
        return $this->anonymizeInformation ??
            $this->anonymizeInformation = $this->blockFactory->createBlock(
                Block::class,
                ['data' => ['block_id' => $this->config->getAnonymizeInformationBlockId()]]
            )->toHtml();
    }
}
