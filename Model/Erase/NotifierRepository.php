<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Erase;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;

/**
 * @api
 */
class NotifierRepository
{
    public function __construct(private array $notifiers = []) {}

    /**
     * @throws NoSuchEntityException
     */
    public function get(string $entityType, string $action): NotifierInterface
    {
        return $this->notifiers[$action][$entityType] ?? throw $this->createException($entityType, $action);
    }

    private function createException(string $entityType, string $action): NoSuchEntityException
    {
        return new NoSuchEntityException(
            new Phrase('No such notifier for entity type "' . $entityType . '" and action "' . $action . '"')
        );
    }
}
