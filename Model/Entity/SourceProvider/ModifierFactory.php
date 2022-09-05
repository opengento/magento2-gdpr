<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity\SourceProvider;

use Magento\Framework\ObjectManagerInterface;

/**
 * @api
 */
final class ModifierFactory
{
    /**
     * @var string[]
     */
    private array $modifiers;

    private ObjectManagerInterface $objectManager;

    /**
     * @param string[] $modifiers
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        array $modifiers,
        ObjectManagerInterface $objectManager
    ) {
        $this->modifiers = $modifiers;
        $this->objectManager = $objectManager;
    }

    public function get(string $entityType): ModifierInterface
    {
        return $this->objectManager->get($this->modifiers[$entityType] ?? $this->modifiers['default']);
    }
}
