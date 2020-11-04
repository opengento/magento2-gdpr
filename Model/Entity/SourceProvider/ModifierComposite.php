<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity\SourceProvider;

use Magento\Framework\Api\Filter;
use Magento\Framework\Data\Collection;
use function array_values;

/**
 * Class ModifierComposite
 * @api
 */
final class ModifierComposite implements ModifierInterface
{
    /**
     * @var ModifierInterface[]
     */
    private $modifiers;

    /**
     * @param ModifierInterface[] $modifiers
     */
    public function __construct(
        array $modifiers
    ) {
        $this->modifiers = (static function (ModifierInterface ...$modifiers): array {
            return $modifiers;
        })(...array_values($modifiers));
    }

    public function apply(Collection $collection, Filter $filter): void
    {
        foreach ($this->modifiers as $modifier) {
            $modifier->apply($collection, $filter);
        }
    }
}
