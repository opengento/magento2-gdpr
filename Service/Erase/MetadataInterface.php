<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Erase;

/**
 * Interface MetadataInterface
 * @api
 */
interface MetadataInterface
{
    /**
     * Retrieve the erase processor associated by components
     *
     * @param string|null $scopeCode
     * @return array
     */
    public function getComponentsProcessors(?string $scopeCode = null): array;

    /**
     * Retrieve the erase processor by component
     *
     * @param string $component
     * @param string|null $scopeCode
     * @return string
     */
    public function getComponentProcessor(string $component, ?string $scopeCode = null): string;
}
