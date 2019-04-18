<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

namespace Opengento\Gdpr\Service\Export\Processor\Entity;

/**
 * Interface ConfigInterface
 * @api
 */
interface ConfigInterface
{
    /**
     * Retrieve the allowed attributes to export
     *
     * @return string[]
     */
    public function getAttributes(): array;
}
