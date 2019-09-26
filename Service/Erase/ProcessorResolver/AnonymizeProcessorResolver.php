<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Erase\ProcessorResolver;

use InvalidArgumentException;
use Magento\Framework\ObjectManager\TMap;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;
use Opengento\Gdpr\Service\Erase\ProcessorResolverInterface;
use function sprintf;

final class AnonymizeProcessorResolver implements ProcessorResolverInterface
{
    /**
     * @var TMap
     */
    private $processorPool;

    public function __construct(
        TMap $processorPool
    ) {
        $this->processorPool = $processorPool;
    }

    public function resolve(string $component): ProcessorInterface
    {
        if (!$this->processorPool->offsetExists($component)) {
            throw new InvalidArgumentException(sprintf('Unknown processor type "%s".', $component));
        }

        return $this->processorPool->offsetGet($component);
    }
}
