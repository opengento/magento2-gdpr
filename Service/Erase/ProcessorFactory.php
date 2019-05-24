<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Erase;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class ProcessorFactory
 */
final class ProcessorFactory
{
    /**
     * @var string[]
     */
    private $processors;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param string[] $processors
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        array $processors,
        ObjectManagerInterface $objectManager
    ) {
        $this->processors = $processors;
        $this->objectManager = $objectManager;
    }

    /**
     * Retrieve the processor instance by its key code
     *
     * @param string $processorCode
     * @return \Opengento\Gdpr\Service\Erase\ProcessorInterface
     */
    public function get(string $processorCode): ProcessorInterface
    {
        if (!isset($this->processors[$processorCode])) {
            throw new \InvalidArgumentException(\sprintf('Unknown renderer type "%s".', $processorCode));
        }

        return $this->objectManager->get($this->processors[$processorCode]);
    }
}
