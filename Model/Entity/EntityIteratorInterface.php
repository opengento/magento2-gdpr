<?php
/**
 * Copyright © 2019 Opengento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

namespace Opengento\Gdpr\Model\Entity;

/**
 * Interface EntityIteratorInterface
 * @api
 */
interface EntityIteratorInterface
{
    /**
     * Iterate through the entity object values
     *
     * @param object $entity
     * @return void
     */
    public function iterate($entity): void;
}
