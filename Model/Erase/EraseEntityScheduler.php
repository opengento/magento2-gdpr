<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Erase;

use Magento\Framework\Api\Filter;
use Opengento\Gdpr\Api\EraseEntityManagementInterface;
use Opengento\Gdpr\Model\Entity\SourceProvider\ModifierFactory;
use Opengento\Gdpr\Model\Entity\SourceProviderFactory;

/**
 * Class EraseEntityScheduler
 */
final class EraseEntityScheduler
{
    /**
     * @var \Opengento\Gdpr\Model\Entity\SourceProviderFactory
     */
    private $sourceProviderFactory;

    /**
     * @var \Opengento\Gdpr\Model\Entity\SourceProvider\ModifierFactory
     */
    private $sourceProviderModifierFactory;

    /**
     * @var \Opengento\Gdpr\Api\EraseEntityManagementInterface
     */
    private $eraseManagement;

    /**
     * @param \Opengento\Gdpr\Model\Entity\SourceProviderFactory $sourceProviderFactory
     * @param \Opengento\Gdpr\Model\Entity\SourceProvider\ModifierFactory $sourceProviderModifierFactory
     * @param \Opengento\Gdpr\Api\EraseEntityManagementInterface $eraseManagement
     */
    public function __construct(
        SourceProviderFactory $sourceProviderFactory,
        ModifierFactory $sourceProviderModifierFactory,
        EraseEntityManagementInterface $eraseManagement
    ) {
        $this->sourceProviderFactory = $sourceProviderFactory;
        $this->sourceProviderModifierFactory = $sourceProviderModifierFactory;
        $this->eraseManagement = $eraseManagement;
    }

    /**
     * Schedule filtered entities to erase
     *
     * @param string[] $entityTypes
     * @param \Magento\Framework\Api\Filter $filter
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function schedule(array $entityTypes, Filter $filter): void
    {
        foreach ($this->collectEntityIds($entityTypes, $filter) as $entityType => $entityIds) {
            foreach ($entityIds as $entityId) {
                $this->eraseManagement->create((int) $entityId, $entityType);
            }
        }
    }

    /**
     * Collect available entity ids by entity type
     *
     * @param string[] $entityTypes
     * @param \Magento\Framework\Api\Filter $filter
     * @return \Generator
     */
    private function collectEntityIds(array $entityTypes, Filter $filter): \Generator
    {
        foreach ($entityTypes as $entityType) {
            $source = $this->sourceProviderFactory->create($entityType);
            $this->sourceProviderModifierFactory->get($entityType)->apply($source, $filter);

            yield $entityType => $source->getAllIds();
        }
    }
}
