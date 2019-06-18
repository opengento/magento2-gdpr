<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

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
     * @param int $entityId
     * @param array $data
     * @return array
     */
    public function execute(int $entityId, array $data): array;
}
