<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

namespace Opengento\Gdpr\Service\Anonymize;

/**
 * Interface ProcessorInterface
 * @api
 */
interface ProcessorInterface
{
    /**
     * Execute the anonymize processor for the given entity ID.
     * It allows to anonymize the related data.
     *
     * @param int $customerId
     * @return bool
     */
    public function execute(int $customerId): bool;
}
