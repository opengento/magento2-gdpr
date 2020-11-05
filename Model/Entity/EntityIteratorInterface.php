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
    public function iterate(object $entity): void;
}
