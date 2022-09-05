<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export;

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
    private array $exporters;

    private ObjectManagerInterface $objectManager;

    /**
     * @param string[] $exporters
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        array $exporters,
        ObjectManagerInterface $objectManager
    ) {
        $this->exporters = $exporters;
        $this->objectManager = $objectManager;
    }

    public function get(string $entityType): ProcessorInterface
    {
        if (!isset($this->exporters[$entityType])) {
            throw new InvalidArgumentException(sprintf('Unknown exporter for entity type "%s".', $entityType));
        }

        return $this->objectManager->get($this->exporters[$entityType]);
    }
}
