<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

namespace Opengento\Gdpr\Service\Export;

/**
 * Interface ProcessorInterface
 * @api
 */
interface ProcessorInterface
{
    /**
     * Execute the export processor for the given entity ID.
     * It allows to retrieve the related data as an array.
     *
     * @param int $customerId
     * @param array $data
     * @return array
     */
    public function execute(int $customerId, array $data): array;
}
