<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity\SourceProvider;

use Magento\Framework\Api\Filter;
use Magento\Framework\Data\Collection;

/**
 * Interface ModifierInterface
 * @api
 */
interface ModifierInterface
{
    /**
     * Apply custom filter on the source provider instance
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @param \Magento\Framework\Api\Filter $filter
     * @return void
     */
    public function apply(Collection $collection, Filter $filter): void;
}
