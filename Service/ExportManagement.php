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
     * @param string $customerEmail
     * @return array
     */
    public function execute(string $customerEmail): array
    {
        $data = [];

        /** @var \Opengento\Gdpr\Service\Export\ProcessorInterface $processor */
        foreach ($this->processorPool as $processor) {
            $data = $processor->execute($customerEmail, $data);
        }

        return $data;
    }

    /**
     * Execute an export processor by name
     *
     * @param string $processorName
     * @param string $customerEmail
     * @return array
     */
    public function executeProcessor(string $processorName, string $customerEmail): array
    {
        if ($this->processorPool->offsetExists($processorName)) {
            throw new \LogicException('The processor "' . $processorName . '" is not registered.');
        }

        /** @var \Opengento\Gdpr\Service\Export\ProcessorInterface $processor */
        $processor = $this->processorPool->offsetGet($processorName);
        return $processor->execute($customerEmail, []);
    }
}
