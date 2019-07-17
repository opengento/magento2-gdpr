<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api;

use Opengento\Gdpr\Api\Data\EraseEntityInterface;

/**
 * Interface EraseSalesInformationInterface
 * @api
 */
interface EraseSalesInformationInterface
{
    /**
     * Schedule the sales information erasure for an entity
     *
     * @param int $entityId
     * @param string $entityType
     * @param \DateTime $lastActive
     * @return \Opengento\Gdpr\Api\Data\EraseEntityInterface
     */
    public function scheduleEraseEntity(int $entityId, string $entityType, \DateTime $lastActive): EraseEntityInterface;

    /**
     * Check if the date time is under the sales information lifetime limit
     *
     * @param \DateTime $lastActive
     * @return bool
     */
    public function isAlive(\DateTime $lastActive): bool;
}
