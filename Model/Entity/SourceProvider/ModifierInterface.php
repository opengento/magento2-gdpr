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
 * @api
 */
interface ModifierInterface
{
    public function apply(Collection $collection, Filter $filter): void;
}
