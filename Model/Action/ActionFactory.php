<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action;

use InvalidArgumentException;
use Magento\Framework\ObjectManagerInterface;
use Opengento\Gdpr\Api\ActionInterface;
use function sprintf;

/**
 * @api
 */
final class ActionFactory
{
    /**
     * @var string[]
     */
    private $actions;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ActionInterface[]
     */
    private $instances;

    public function __construct(
        array $actions,
        ObjectManagerInterface $objectManager
    ) {
        $this->actions = $actions;
        $this->objectManager = $objectManager;
        $this->instances = [];
    }

    public function get(string $type): ActionInterface
    {
        if (!isset($this->instances[$type])) {
            if (!isset($this->actions[$type])) {
                throw new InvalidArgumentException(sprintf('Unknown action for type "%s".', $type));
            }

            $this->instances[$type] = $this->objectManager->create($this->actions[$type], ['type' => $type]);
        }

        return $this->instances[$type];
    }
}
