<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity;

use Magento\Framework\Data\Collection;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class SourceProviderFactory
 * @api
 */
final class SourceProviderFactory
{
    /**
     * @var string[]
     */
    private $sourceProviders;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param string[] $sourceProviders
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        array $sourceProviders,
        ObjectManagerInterface $objectManager
    ) {
        $this->sourceProviders = $sourceProviders;
        $this->objectManager = $objectManager;
    }

    /**
     * Create a new source provider by entity type
     *
     * @param string $entityType
     * @return \Magento\Framework\Data\Collection
     */
    public function create(string $entityType): Collection
    {
        if (!isset($this->sourceProviders[$entityType])) {
            throw new \InvalidArgumentException(\sprintf('Unknown source provider for entity type "%s".', $entityType));
        }

        return $this->objectManager->create($this->sourceProviders[$entityType]);
    }
}
