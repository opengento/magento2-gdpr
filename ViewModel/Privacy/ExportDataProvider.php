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

final class ExportDataProvider extends DataObject implements ArgumentInterface
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

    public function isExportEnabled(): bool
    {
        return $this->config->isExportEnabled();
    }

    public function getExportInformationHtml(): string
    {
        if (!$this->hasData('export_information')) {
            $block = $this->blockFactory->createBlock(
                Block::class,
                ['data' => ['block_id' => $this->config->getExportInformationBlockId()]]
            );
            $this->setData('export_information', $block->toHtml());
        }

        return (string) $this->_getData('export_information');
    }
}
