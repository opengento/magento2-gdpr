<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity;

use function array_keys;
use function array_merge;

/**
 * @api
 */
final class EntityTypeList
{
    /**
     * @var string[][]
     */
    private array $list;

    /**
     * @var string[]|null
     */
    private ?array $entityTypes;

    public function __construct(
        array $list
    ) {
        $this->list = $list;
    }

    public function getList(): array
    {
        return $this->list;
    }

    public function getEntityTypes(): array
    {
        $entityTypes = [];

        foreach ($this->getList() as $types) {
            $entityTypes[] = array_keys($types);
        }

        return $this->entityTypes ??= array_merge([], ...$entityTypes);
    }
}
