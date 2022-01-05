<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Erase;

use InvalidArgumentException;
use Magento\Framework\ObjectManagerInterface;
use function sprintf;

/**
 * @api
 */
final class ProcessorResolverFactory
{
    /**
     * @var string[]
     */
    private array $processorResolvers;

    private ObjectManagerInterface $objectManager;

    /**
     * @param string[] $processorResolvers
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        array $processorResolvers,
        ObjectManagerInterface $objectManager
    ) {
        $this->processorResolvers = $processorResolvers;
        $this->objectManager = $objectManager;
    }

    public function get(string $processorCode): ProcessorResolverInterface
    {
        if (!isset($this->processorResolvers[$processorCode])) {
            throw new InvalidArgumentException(sprintf('Unknown renderer type "%s".', $processorCode));
        }

        return $this->objectManager->get($this->processorResolvers[$processorCode]);
    }
}
