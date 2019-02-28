<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Processor\Utils;

/**
 * Class DataFilterProcessor
 */
final class DataFilterProcessor implements DataFilterProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(array $scheme, array $data): array
    {
        return \array_filter(
            $data,
            function ($key) use ($scheme) { return \in_array($key, $scheme, true); },
            \ARRAY_FILTER_USE_KEY
        );
    }
}
