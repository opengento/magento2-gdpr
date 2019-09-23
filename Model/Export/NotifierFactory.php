<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Export;

use Magento\Framework\ObjectManagerInterface;

/**
 * @api
 */
final class NotifierFactory
{
    /**
     * @var string[]
     */
    private $notifiers;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param string[] $notifiers
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        array $notifiers,
        ObjectManagerInterface $objectManager
    ) {
        $this->notifiers = $notifiers;
        $this->objectManager = $objectManager;
    }

    public function get(string $action, string $entityType): NotifierInterface
    {
        if (!isset($this->notifiers[$action][$entityType])) {
            throw new \InvalidArgumentException(
                \sprintf('Unknown notifier for action "%s" and entity type "%s".', $action, $entityType)
            );
        }

        return $this->objectManager->get($this->notifiers[$action][$entityType]);
    }
}
