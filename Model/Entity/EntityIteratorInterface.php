<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity;

/**
 * @api
 */
interface EntityIteratorInterface
{
    /**
     * @param object $entity
     * @return void
     */
    public function iterate($entity): void;
}
