<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Erase;

use Generator;
use Magento\Framework\Api\Filter;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Opengento\Gdpr\Api\EraseEntityManagementInterface;
use Opengento\Gdpr\Model\Entity\SourceProvider\ModifierFactory;
use Opengento\Gdpr\Model\Entity\SourceProviderFactory;

final class EraseEntityScheduler
{
    /**
     * @var SourceProviderFactory
     */
    private $sourceProviderFactory;

    /**
     * @var ModifierFactory
     */
    private $sourceProviderModifierFactory;

    /**
     * @var EraseEntityManagementInterface
     */
    private $eraseManagement;

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
     * @param string[] $entityTypes
     * @param Filter $filter
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function schedule(array $entityTypes, Filter $filter): void
    {
        /**
         * @var string $entityType
         * @var string[] $entityIds
         */
        foreach ($this->collectEntityIds($entityTypes, $filter) as $entityType => $entityIds) {
            foreach ($entityIds as $entityId) {
                $this->eraseManagement->create((int) $entityId, $entityType);
            }
        }
    }

    /**
     * @param string[] $entityTypes
     * @param Filter $filter
     * @return Generator
     */
    private function collectEntityIds(array $entityTypes, Filter $filter): Generator
    {
        foreach ($entityTypes as $entityType) {
            $source = $this->sourceProviderFactory->create($entityType);
            $this->sourceProviderModifierFactory->get($entityType)->apply($source, $filter);

            yield $entityType => $source->getAllIds();
        }
    }
}
