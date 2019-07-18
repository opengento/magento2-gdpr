<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class EntityCheckerFactory
 * @api
 */
final class EntityCheckerFactory
{
    /**
     * @var string[]
     */
    private $checkers;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param string[] $checkers
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        array $checkers,
        ObjectManagerInterface $objectManager
    ) {
        $this->checkers = $checkers;
        $this->objectManager = $objectManager;
    }

    /**
     * Retrieve the export processor by entity type
     *
     * @param string $entityType
     * @return \Opengento\Gdpr\Model\Entity\EntityCheckerInterface
     */
    public function get(string $entityType): EntityCheckerInterface
    {
        if (!isset($this->checkers[$entityType])) {
            throw new \InvalidArgumentException(\sprintf('Unknown checker for entity type "%s".', $entityType));
        }

        return $this->objectManager->get($this->checkers[$entityType]);
    }
}
