<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Erase;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class NotifierFactory
 * @api
 */
final class NotifierFactory
{
    /**
     * @var string[]
     */
    private $notifiers;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param string[] $notifiers
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        array $notifiers,
        ObjectManagerInterface $objectManager
    ) {
        $this->notifiers = $notifiers;
        $this->objectManager = $objectManager;
    }

    /**
     * Retrieve the notifier instance by action and entity type
     *
     * @param string $action
     * @param string $entityType
     * @return \Opengento\Gdpr\Model\Erase\NotifierInterface
     */
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
