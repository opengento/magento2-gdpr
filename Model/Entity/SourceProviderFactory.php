<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity;

use InvalidArgumentException;
use Magento\Framework\Data\Collection;
use Magento\Framework\ObjectManagerInterface;
use function sprintf;

/**
 * @api
 */
final class SourceProviderFactory
{
    /**
     * @var string[]
     */
    private $sourceProviders;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param string[] $sourceProviders
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        array $sourceProviders,
        ObjectManagerInterface $objectManager
    ) {
        $this->sourceProviders = $sourceProviders;
        $this->objectManager = $objectManager;
    }

    public function create(string $entityType): Collection
    {
        if (!isset($this->sourceProviders[$entityType])) {
            throw new InvalidArgumentException(sprintf('Unknown source provider for entity type "%s".', $entityType));
        }

        return $this->objectManager->create($this->sourceProviders[$entityType]);
    }
}
