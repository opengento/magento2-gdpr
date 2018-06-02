<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service;

use Magento\Framework\ObjectManager\TMap;

/**
 * Class ExportManagement
 * @api
 */
class ExportManagement
{
    /**
     * @var \Magento\Framework\ObjectManager\TMap
     */
    private $processorPool;

    /**
     * @param \Magento\Framework\ObjectManager\TMap $processorPool
     */
    public function __construct(
        TMap $processorPool
    ) {
        $this->processorPool = $processorPool;
    }

    /**
     * Export all data related to a given entity ID
     *
     * @param int $entityId
     * @return array
     */
    public function execute(int $entityId): array
    {
        $data = [];

        /** @var \Opengento\Gdpr\Service\Export\ProcessorInterface $processor */
        foreach ($this->processorPool as $processor) {
            $data = $processor->execute($entityId, $data);
        }

        return $data;
    }
}
