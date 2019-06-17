<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Erase\ProcessorResolver;

use Magento\Framework\ObjectManager\TMap;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;
use Opengento\Gdpr\Service\Erase\ProcessorResolverInterface;

/**
 * Class DeleteProcessor
 */
final class DeleteProcessorResolver implements ProcessorResolverInterface
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
    public function resolve(string $component): ProcessorInterface
    {
        if (!$this->processorPool->offsetExists($component)) {
            throw new \InvalidArgumentException(\sprintf('Unknown processor type "%s".', $component));
        }

        return $this->processorPool->offsetGet($component);
    }
}
