<?php
/**
 * Copyright © 2019 Opengento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

namespace Opengento\Gdpr\Model\Entity;

/**
 * Interface EntityValueProcessorInterface
 * @api
 */
interface EntityValueProcessorInterface
{
    /**
     * Process the entity by passing it a value and its key
     *
     * @param object $entity
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function process($entity, string $key, $value): void;
}
