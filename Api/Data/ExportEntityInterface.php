<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api\Data;

/**
 * Interface ExportEntityInterface
 * @api
 */
interface ExportEntityInterface
{
    /**
     * Retrieve the entity ID to export
     *
     * @return int
     */
    public function getEntityId(): int;

    /**
     * Retrieve the entity type to export
     *
     * @return string
     */
    public function getEntityType(): string;

    /**
     * Retrieve the file name to export the data to
     *
     * @return string
     */
    public function getFileName(): string;
}
