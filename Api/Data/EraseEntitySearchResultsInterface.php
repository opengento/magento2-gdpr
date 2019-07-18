<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface EraseEntitySearchResultsInterface
 * @api
 */
interface EraseEntitySearchResultsInterface extends SearchResultsInterface
{
    /**
     * Retrieve the erase entity schedulers list
     *
     * @return \Opengento\Gdpr\Api\Data\EraseEntityInterface[]
     */
    public function getItems(): array;

    /**
     * Set the erase entity schedulers list
     *
     * @param \Opengento\Gdpr\Api\Data\EraseEntityInterface[] $items
     * @return \Opengento\Gdpr\Api\Data\EraseEntitySearchResultsInterface
     */
    public function setItems(array $items): EraseEntitySearchResultsInterface;
}
