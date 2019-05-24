<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface EraseCustomerSearchResultsInterface
 * @api
 */
interface EraseCustomerSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Retrieve the erase customer schedulers list
     *
     * @return \Opengento\Gdpr\Api\Data\EraseCustomerInterface[]
     */
    public function getItems(): array;

    /**
     * Set the erase customer schedulers list
     *
     * @param \Opengento\Gdpr\Api\Data\EraseCustomerInterface[] $items
     * @return \Opengento\Gdpr\Api\Data\EraseCustomerSearchResultsInterface
     */
    public function setItems(array $items): EraseCustomerSearchResultsInterface;
}
