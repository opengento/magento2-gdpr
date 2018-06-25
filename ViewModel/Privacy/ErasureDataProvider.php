<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\ViewModel\Privacy;

use Magento\Cms\Block\Block;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\BlockFactory;
use Opengento\Gdpr\Helper\Data;
use Opengento\Gdpr\Model\Config;
use Opengento\Gdpr\Service\ErasureStrategy;

/**
 * Class ErasureDataProvider
 */
class ErasureDataProvider extends DataObject implements ArgumentInterface
{
    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @var \Opengento\Gdpr\Helper\Data
     */
    private $helperData;

    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    private $blockFactory;

    /**
     * @param \Opengento\Gdpr\Model\Config $config
     * @param \Opengento\Gdpr\Helper\Data $helperData
     * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
     * @param array $data
     */
    public function __construct(
        Config $config,
        Data $helperData,
        BlockFactory $blockFactory,
        array $data = []
    ) {
        $this->config = $config;
        $this->helperData = $helperData;
        $this->blockFactory = $blockFactory;
        parent::__construct($data);
    }

    /**
     * Check if the erasure is enabled
     *
     * @return bool
     */
    public function isErasureEnabled(): bool
    {
        return $this->config->isExportEnabled();
    }

    /**
     * Retrieve the erase information html
     *
     * @return string
     */
    public function getErasureInformation(): string
    {
        if (!$this->hasData('erasure_information')) {
            $block = $this->blockFactory->createBlock(
                Block::class,
                ['data' => ['block_id' => $this->config->getErasureInformationBlockId()]]
            );
            $this->setData('erasure_information', $block->toHtml());
        }

        return (string) $this->_getData('erasure_information');
    }

    /**
     * Check if the erasure is already planned
     *
     * @return bool
     */
    public function isErasureScheduled(): bool
    {
        return $this->helperData->isAccountToBeDeleted();
    }

    /**
     * Check if the snonymize strategy is enabled
     *
     * @return bool
     */
    public function isAnonymizeStrategy(): bool
    {
        return ($this->config->getDefaultStrategy() === ErasureStrategy::STRATEGY_ANONYMIZE);
    }

    /**
     * Retrieve the anonymize information html
     *
     * @return string
     */
    public function getAnonymizeInformation(): string
    {
        if (!$this->hasData('anonymize_information')) {
            $block = $this->blockFactory->createBlock(
                Block::class,
                ['data' => ['block_id' => $this->config->getAnonymizeInformationBlockId()]]
            );
            $this->setData('anonymize_information', $block->toHtml());
        }

        return (string) $this->_getData('anonymize_information');
    }
}
