<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Erase;

/**
 * Interface ProcessorResolverInterface
 * @api
 */
interface ProcessorResolverInterface
{
    /**
     * Resolve the erase processor for the given component
     *
     * @param string $component
     * @return \Opengento\Gdpr\Service\Erase\ProcessorInterface
     */
    public function resolve(string $component): ProcessorInterface;
}
