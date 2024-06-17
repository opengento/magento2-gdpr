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

class EraseEntityScheduler
{
    private SourceProviderFactory $srcProviderFactory;

    private ModifierFactory $modifierFactory;

    private EraseEntityManagementInterface $eraseManagement;

    public function __construct(
        SourceProviderFactory $srcProviderFactory,
        ModifierFactory $modifierFactory,
        EraseEntityManagementInterface $eraseManagement
    ) {
        $this->srcProviderFactory = $srcProviderFactory;
        $this->modifierFactory = $modifierFactory;
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
            $source = $this->srcProviderFactory->create($entityType);
            $this->modifierFactory->get($entityType)->apply($source, $filter);

            yield $entityType => $source->getAllIds();
        }
    }
}
