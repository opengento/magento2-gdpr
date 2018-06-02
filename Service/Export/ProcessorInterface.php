<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

namespace Flurrybox\EnhancedPrivacy\Service\Export;

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
     * @param int $entityId
     * @param array $data
     * @return array
     */
    public function execute(string $customerEmail, array $data): array;
}
