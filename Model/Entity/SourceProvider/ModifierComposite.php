<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity\SourceProvider;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Store\Api\Data\WebsiteInterface;

/**
 * @api
 */
class ModifierComposite implements ModifierInterface
{
    public function __construct(private array $modifiers = []) {}

    public function apply(AbstractDb $collection, WebsiteInterface $website): void
    {
        foreach ($this->modifiers as $modifier) {
            $modifier->apply($collection, $website);
        }
    }
}
