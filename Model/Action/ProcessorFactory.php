<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action;

use Magento\Framework\ObjectManagerInterface;

/**
 * @api
 */
final class ProcessorFactory
{
    /**
     * @var string[]
     */
    private $processors;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param string[] $processors
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        array $processors,
        ObjectManagerInterface $objectManager
    ) {
        $this->processors = $processors;
        $this->objectManager = $objectManager;
    }

    public function get(string $type): ProcessorInterface
    {
        if (!isset($this->processors[$type])) {
            throw new \InvalidArgumentException(\sprintf('Unknown processor for action "%s".', $type));
        }

        return $this->objectManager->get($this->processors[$type]);
    }
}
