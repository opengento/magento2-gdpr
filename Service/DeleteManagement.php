<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service;

use Magento\Framework\ObjectManager\TMap;

/**
 * Class DeleteManagement
 * @api
 */
final class DeleteManagement
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
     * Delete all data related to a given entity ID
     *
     * @param int $customerId
     * @return bool
     */
    public function execute(int $customerId): bool
    {
        /** @var \Opengento\Gdpr\Service\Delete\ProcessorInterface $processor */
        foreach ($this->processorPool as $processor) {
            if (!$processor->execute($customerId)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Execute a delete processor by name
     *
     * @param string $processorName
     * @param int $customerId
     * @return bool
     */
    public function executeProcessor(string $processorName, int $customerId): bool
    {
        if (!$this->processorPool->offsetExists($processorName)) {
            throw new \InvalidArgumentException(\sprintf('Unknown processor type "%s".', $processorName));
        }

        /** @var \Opengento\Gdpr\Service\Delete\ProcessorInterface $processor */
        $processor = $this->processorPool->offsetGet($processorName);

        return $processor->execute($customerId);
    }
}
