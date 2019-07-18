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
 * @api
 */
final class ProcessorFactory
{
    /**
     * @var string[]
     */
    private $erasers;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param string[] $erasers
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        array $erasers,
        ObjectManagerInterface $objectManager
    ) {
        $this->erasers = $erasers;
        $this->objectManager = $objectManager;
    }

    /**
     * Create a new eraser processor
     *
     * @param string $entityType
     * @return \Opengento\Gdpr\Service\Erase\ProcessorInterface
     */
    public function get(string $entityType): ProcessorInterface
    {
        if (!isset($this->erasers[$entityType])) {
            throw new \InvalidArgumentException(\sprintf('Unknown eraser for entity type "%s".', $entityType));
        }

        return $this->objectManager->get($this->erasers[$entityType]);
    }
}
