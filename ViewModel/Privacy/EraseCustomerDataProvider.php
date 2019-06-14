<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\ViewModel\Privacy;

use Magento\Cms\Block\Block;
use Magento\Customer\Model\Session;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\BlockFactory;
use Opengento\Gdpr\Api\EraseCustomerCheckerInterface;
use Opengento\Gdpr\Model\Config;

/**
 * Class ErasureDataProvider
 */
final class ErasureDataProvider extends DataObject implements ArgumentInterface
{
    /**
     * @var \Opengento\Gdpr\Api\EraseCustomerCheckerInterface
     */
    private $eraseCustomerChecker;

    /**
     * @var \Opengento\Gdpr\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $session;

    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    private $blockFactory;

    /**
     * @param \Opengento\Gdpr\Api\EraseCustomerCheckerInterface $eraseCustomerChecker
     * @param \Opengento\Gdpr\Model\Config $config
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
     * @param array $data
     */
    public function __construct(
        EraseCustomerCheckerInterface $eraseCustomerChecker,
        Config $config,
        Session $session,
        BlockFactory $blockFactory,
        array $data = []
    ) {
        $this->eraseCustomerChecker = $eraseCustomerChecker;
        $this->config = $config;
        $this->session = $session;
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
     * Check if the erasure is already planned and could be canceled
     *
     * @return bool
     */
    public function canCancel(): bool
    {
        if (!$this->hasData('can_cancel')) {
            $this->setData('can_cancel', $this->eraseCustomerChecker->canCancel((int) $this->session->getCustomerId()));
        }

        return (bool) $this->_getData('can_cancel');
    }

    /**
     * Check if the erasure can be planned and processed
     *
     * @return bool
     */
    public function canCreate(): bool
    {
        if (!$this->hasData('can_create')) {
            $this->setData('can_create', $this->eraseCustomerChecker->canCreate((int) $this->session->getCustomerId()));
        }

        return (bool) $this->_getData('can_create');
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
