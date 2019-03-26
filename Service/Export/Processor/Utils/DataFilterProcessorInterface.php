<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Processor\Utils;

/**
 * Interface DataFilterProcessorInterface
 * @api
 */
interface DataFilterProcessorInterface
{
    /**
     * Filter data by allowed scheme keys
     *
     * @param array $scheme
     * @param array $data
     * @return array
     */
    public function execute(array $scheme, array $data): array;
}
