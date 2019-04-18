<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

namespace Opengento\Gdpr\Service\Export\Processor\Entity;

/**
 * Interface DataCollectorInterface
 * @api
 */
interface DataCollectorInterface
{
    /**
     * Collect data from the entity object
     *
     * @param object $entity
     * @return array
     */
    public function collect($entity): array;
}
