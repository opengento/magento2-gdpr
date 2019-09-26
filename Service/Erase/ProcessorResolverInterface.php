<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Erase;

/**
 * Resolve the erase processor for the given component
 * @api
 */
interface ProcessorResolverInterface
{
    public function resolve(string $component): ProcessorInterface;
}
