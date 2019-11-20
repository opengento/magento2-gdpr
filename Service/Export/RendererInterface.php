<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export;

/**
 * @api
 */
interface RendererInterface
{
    public function render(array $data): string;

    public function saveData(string $fileName, array $data): string;
}
