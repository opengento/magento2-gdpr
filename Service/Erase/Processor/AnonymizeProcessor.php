<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Erase\Processor;

use Magento\Framework\ObjectManager\TMap;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;

/**
 * Class AnonymizeProcessor
 */
final class AnonymizeProcessor implements ProcessorInterface
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
     * @inheritdoc
     */
    public function execute(string $component, int $customerId): bool
    {
        if (!$this->processorPool->offsetExists($component)) {
            throw new \InvalidArgumentException(\sprintf('Unknown processor type "%s".', $component));
        }

        /** @var \Opengento\Gdpr\Service\Anonymize\ProcessorInterface $processor */
        $processor = $this->processorPool->offsetGet($component);

        return $processor->execute($customerId);
    }
}
