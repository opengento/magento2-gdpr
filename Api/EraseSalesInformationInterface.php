<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api;

use DateTime;
use Opengento\Gdpr\Api\Data\EraseEntityInterface;

/**
 * @api
 * @todo should be internal? No use case of external usage.
 */
interface EraseSalesInformationInterface
{
    public function scheduleEraseEntity(int $entityId, string $entityType, DateTime $lastActive): EraseEntityInterface;

    public function isAlive(DateTime $lastActive): bool;
}
