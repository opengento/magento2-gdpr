<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api;

/**
 * Interface EraseInterface
 * @api
 */
interface EraseInterface
{
    /**
     * Erase the customer related personal data
     *
     * @param int $customerId
     * @return bool
     */
    public function erase(int $customerId): bool;
}
