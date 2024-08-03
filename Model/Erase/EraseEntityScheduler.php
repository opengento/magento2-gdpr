<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Erase;

use Generator;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Api\Data\WebsiteInterface;
use Opengento\Gdpr\Api\EraseEntityManagementInterface;
use Opengento\Gdpr\Model\Entity\SourceProvider\ModifierFactory;
use Opengento\Gdpr\Model\Entity\SourceProviderFactory;

class EraseEntityScheduler
{
    public function __construct(
        private SourceProviderFactory $srcProviderFactory,
        private ModifierFactory $modifierFactory,
        private EraseEntityManagementInterface $eraseManagement
    ) {}

    /**
     * @param string[] $entityTypes
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function schedule(array $entityTypes, WebsiteInterface $website): void
    {
        /**
         * @var string $entityType
         * @var string[] $entityIds
         */
        foreach ($this->collectEntityIds($entityTypes, $website) as $entityType => $entityIds) {
            foreach ($entityIds as $entityId) {
                $this->eraseManagement->create((int)$entityId, $entityType);
            }
        }
    }

    /**
     * @param string[] $entityTypes
     */
    private function collectEntityIds(array $entityTypes, WebsiteInterface $website): Generator
    {
        foreach ($entityTypes as $entityType) {
            $source = $this->srcProviderFactory->create($entityType);
            $this->modifierFactory->get($entityType)->apply($source, $website);

            yield $entityType => $source->getAllIds();
        }
    }
}
