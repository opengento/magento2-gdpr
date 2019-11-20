<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity;

/**
 * @api
 */
interface MetadataInterface
{
    /**
     * Retrieve the registered attributes
     *
     * @param string|null $scopeCode [optional] Current scope will be used.
     * @return string[]
     */
    public function getAttributes(?string $scopeCode = null): array;
}
