<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Renderer;

use Magento\Framework\Filesystem;
use function is_array;
use function rtrim;
use function str_replace;

final class CsvRenderer extends AbstractRenderer
{
    public function __construct(
        Filesystem $filesystem
    ) {
        parent::__construct($filesystem, 'csv');
    }

    public function render(array $data): string
    {
        $csv = '';

        foreach ($data as $key => $value) {
            $csv .= is_array($value)
                ? $key . ',' . rtrim($this->render($value), ',') . \PHP_EOL
                : '"' . str_replace('"', '""', $value) . '",';
        }

        return $csv;
    }
}
