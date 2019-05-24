<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

namespace Opengento\Gdpr\Service\Erase;

/**
 * Interface ProcessorInterface
 * @api
 */
interface ProcessorInterface
{
    /**
     * Execute the erase processor for the given entity ID.
     * It allows to erase the related data.
     *
     * @param string $component
     * @param int $customerId
     * @return bool
     */
    public function execute(string $component, int $customerId): bool;
}
