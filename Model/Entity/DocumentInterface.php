<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

namespace Opengento\Gdpr\Model\Entity;

/**
 * Interface DocumentInterface
 * @api
 */
interface DocumentInterface
{
    /**
     * Set the document data
     *
     * @param array $data
     * @return void
     */
    public function setData(array $data): void;

    /**
     * Append data by key to the document
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function addData(string $key, $value): void;

    /**
     * Retrieve the data and empties the document
     *
     * @return array
     */
    public function getData(): array;
}
