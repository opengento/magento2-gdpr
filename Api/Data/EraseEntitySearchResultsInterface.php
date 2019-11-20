<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface EraseEntitySearchResultsInterface extends SearchResultsInterface
{
    /**
     * Retrieve the erase entity schedulers list
     *
     * @return EraseEntityInterface[]
     */
    public function getItems(): array;

    /**
     * Set the erase entity schedulers list
     *
     * @param EraseEntityInterface[] $items
     * @return EraseEntitySearchResultsInterface
     */
    public function setItems(array $items): EraseEntitySearchResultsInterface;
}
