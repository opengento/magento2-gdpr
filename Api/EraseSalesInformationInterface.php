<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api;

use DateTimeInterface;
use Opengento\Gdpr\Api\Data\EraseEntityInterface;

/**
 * @api
 */
interface EraseSalesInformationInterface
{
    public function scheduleEraseEntity(int $entityId, string $entityType, DateTimeInterface $lastActive): EraseEntityInterface;

    public function isAlive(DateTimeInterface $lastActive): bool;
}
