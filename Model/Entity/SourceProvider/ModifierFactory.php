<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity\SourceProvider;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class ModifierFactory
 * @api
 */
final class ModifierFactory
{
    /**
     * @var string[]
     */
    private $modifiers;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param string[] $modifiers
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        array $modifiers,
        ObjectManagerInterface $objectManager
    ) {
        $this->modifiers = $modifiers;
        $this->objectManager = $objectManager;
    }

    /**
     * Retrieve the source provider modifier by entity type
     *
     * @param string $entityType
     * @return \Opengento\Gdpr\Model\Entity\SourceProvider\ModifierInterface
     */
    public function get(string $entityType): ModifierInterface
    {
        return $this->objectManager->get($this->modifiers[$entityType] ?? $this->modifiers['default']);
    }
}
