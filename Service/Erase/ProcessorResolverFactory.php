<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Erase;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class ProcessorResolverFactory
 */
final class ProcessorResolverFactory
{
    /**
     * @var string[]
     */
    private $processorResolvers;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param string[] $processorResolvers
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        array $processorResolvers,
        ObjectManagerInterface $objectManager
    ) {
        $this->processorResolvers = $processorResolvers;
        $this->objectManager = $objectManager;
    }

    /**
     * Retrieve the processor instance by its key code
     *
     * @param string $processorCode
     * @return \Opengento\Gdpr\Service\Erase\ProcessorResolverInterface
     */
    public function get(string $processorCode): ProcessorResolverInterface
    {
        if (!isset($this->processorResolvers[$processorCode])) {
            throw new \InvalidArgumentException(\sprintf('Unknown renderer type "%s".', $processorCode));
        }

        return $this->objectManager->get($this->processorResolvers[$processorCode]);
    }
}
