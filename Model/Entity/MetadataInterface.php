<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

namespace Opengento\Gdpr\Model\Entity;

/**
 * Interface MetadataInterface
 * @api
 */
interface MetadataInterface
{
    /**
     * Retrieve the allowed attributes to export
     *
     * @param string|null $scopeCode
     * @return string[]
     */
    public function getAttributes(?string $scopeCode = null): array;
}
