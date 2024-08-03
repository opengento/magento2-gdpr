<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity;

use InvalidArgumentException;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\ObjectManagerInterface;

use function sprintf;

/**
 * @api
 */
class SourceProviderFactory
{
    /**
     * @param string[] $sourceProviders
     */
    public function __construct(
        private array $sourceProviders,
        private ObjectManagerInterface $objectManager
    ) {}

    public function create(string $entityType): AbstractDb
    {
        if (!isset($this->sourceProviders[$entityType])) {
            throw new InvalidArgumentException(sprintf('Unknown source provider for entity type "%s".', $entityType));
        }

        return $this->objectManager->create($this->sourceProviders[$entityType]);
    }
}
