<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
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
     * @param string $customerEmail
     * @return bool
     */
    public function execute(string $customerEmail): bool;
}
