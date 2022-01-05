<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity;

use Exception;
use Magento\Framework\EntityManager\TypeResolver;

/**
 * @api
 */
final class EntityTypeResolver
{
    private TypeResolver $typeResolver;

    private EntityTypeList $entityTypeList;

    public function __construct(
        TypeResolver $typeResolver,
        EntityTypeList $entityTypeList
    ) {
        $this->typeResolver = $typeResolver;
        $this->entityTypeList = $entityTypeList;
    }

    /**
     * @param object $entity
     * @return string[]
     * @throws Exception
     */
    public function resolve(object $entity): array
    {
        return $this->entityTypeList->getList()[$this->typeResolver->resolve($entity)] ?? [];
    }
}
