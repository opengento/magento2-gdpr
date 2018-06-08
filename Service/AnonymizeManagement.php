<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service;

use Magento\Framework\ObjectManager\TMap;

/**
 * Class AnonymizeManagement
 * @api
 */
class AnonymizeManagement
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
     * Anonymize all data related to a given entity ID
     *
     * @param string $customerEmail
     * @return bool
     */
    public function execute(string $customerEmail): bool
    {
        /** @var \Opengento\Gdpr\Service\Anonymize\ProcessorInterface $processor */
        foreach ($this->processorPool as $processor) {
            if (!$processor->execute($customerEmail)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Execute an anonymize processor by name
     *
     * @param string $processorName
     * @param string $customerEmail
     * @return bool
     */
    public function executeProcessor(string $processorName, string $customerEmail): bool
    {
        if ($this->processorPool->offsetExists($processorName)) {
            throw new \LogicException('The processor "' . $processorName . '" is not registered.');
        }

        /** @var \Opengento\Gdpr\Service\Anonymize\ProcessorInterface $processor */
        $processor = $this->processorPool->offsetGet($processorName);
        return $processor->execute($customerEmail);
    }
}
