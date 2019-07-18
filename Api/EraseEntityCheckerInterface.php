<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api;

/**
 * Interface EraseEntityCheckerInterface
 * @api
 */
interface EraseEntityCheckerInterface
{
    /**
     * Check if an erase entity scheduler already exists for this entity
     *
     * @param int $entityId
     * @param string $entityType
     * @return bool
     */
    public function exists(int $entityId, string $entityType): bool;

    /**
     * Check if an erase entity scheduler can be created for this entity
     *
     * @param int $entityId
     * @param string $entityType
     * @return bool
     */
    public function canCreate(int $entityId, string $entityType): bool;

    /**
     * Check if an erase entity scheduler can be canceled
     *
     * @param int $entityId
     * @param string $entityType
     * @return bool
     */
    public function canCancel(int $entityId, string $entityType): bool;

    /**
     * Check if an erase entity scheduler can be processed
     *
     * @param int $entityId
     * @param string $entityType
     * @return bool
     */
    public function canProcess(int $entityId, string $entityType): bool;
}
