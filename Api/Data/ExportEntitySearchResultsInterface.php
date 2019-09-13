<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface ExportEntitySearchResultsInterface
 * @api
 */
interface ExportEntitySearchResultsInterface extends SearchResultsInterface
{
    /**
     * Retrieve the export entities list
     *
     * @return \Opengento\Gdpr\Api\Data\ExportEntityInterface[]
     */
    public function getItems(): array;

    /**
     * Set the export entities list
     *
     * @param \Opengento\Gdpr\Api\Data\ExportEntityInterface[] $items
     * @return \Opengento\Gdpr\Api\Data\ExportEntitySearchResultsInterface
     */
    public function setItems(array $items): ExportEntitySearchResultsInterface;
}
