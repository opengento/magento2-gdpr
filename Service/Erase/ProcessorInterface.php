<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Erase;

/**
 * Execute the erase processor for the given entity ID. It allows to erase the related data.
 * @api
 */
interface ProcessorInterface
{
    public function execute(int $entityId): bool;
}
