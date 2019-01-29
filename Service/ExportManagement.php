<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service;

use Magento\Framework\ObjectManager\TMap;

/**
 * Class ExportManagement
 * @api
 */
final class ExportManagement
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
     * @param int $customerId
     * @return array
     */
    public function execute(int $customerId): array
    {
        $data = [];

        /** @var \Opengento\Gdpr\Service\Export\ProcessorInterface $processor */
        foreach ($this->processorPool as $processor) {
            $data = $processor->execute($customerId, $data);
        }

        return $data;
    }

    /**
     * Execute an export processor by name
     *
     * @param string $processorName
     * @param int $customerId
     * @return array
     */
    public function executeProcessor(string $processorName, int $customerId): array
    {
        if (!$this->processorPool->offsetExists($processorName)) {
            throw new \InvalidArgumentException(\sprintf('Unknown processor type "%s".', $processorName));
        }

        /** @var \Opengento\Gdpr\Service\Export\ProcessorInterface $processor */
        $processor = $this->processorPool->offsetGet($processorName);
        return $processor->execute($customerId, []);
    }
}
