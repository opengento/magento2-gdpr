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
final class ProcessorFactory
{
    /**
     * @var string[]
     */
    private $erasers;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        array $erasers,
        ObjectManagerInterface $objectManager
    ) {
        $this->erasers = $erasers;
        $this->objectManager = $objectManager;
    }

    public function get(string $entityType): ProcessorInterface
    {
        if (!isset($this->erasers[$entityType])) {
            throw new InvalidArgumentException(sprintf('Unknown eraser for entity type "%s".', $entityType));
        }

        return $this->objectManager->get($this->erasers[$entityType]);
    }
}
