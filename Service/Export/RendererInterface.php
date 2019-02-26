<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

namespace Opengento\Gdpr\Service\Export;

/**
 * Interface RendererInterface
 * @api
 */
interface RendererInterface
{
    /**
     * Render the data to an output string format
     *
     * @param array $data
     * @return string
     */
    public function render(array $data): string;

    /**
     * Render and save the data to an output format file
     *
     * @param string $fileName
     * @param array $data
     * @return string
     */
    public function saveData(string $fileName, array $data): string;
}
