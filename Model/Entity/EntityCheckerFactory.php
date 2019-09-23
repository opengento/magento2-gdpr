<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity;

use Magento\Framework\ObjectManagerInterface;

/**
 * @api
 */
final class EntityCheckerFactory
{
    /**
     * @var string[]
     */
    private $checkers;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param string[] $checkers
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        array $checkers,
        ObjectManagerInterface $objectManager
    ) {
        $this->checkers = $checkers;
        $this->objectManager = $objectManager;
    }

    public function get(string $entityType): EntityCheckerInterface
    {
        if (!isset($this->checkers[$entityType])) {
            throw new \InvalidArgumentException(\sprintf('Unknown checker for entity type "%s".', $entityType));
        }

        return $this->objectManager->get($this->checkers[$entityType]);
    }
}
