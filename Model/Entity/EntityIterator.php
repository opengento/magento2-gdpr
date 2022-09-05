<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity;

use Exception;
use Magento\Framework\EntityManager\HydratorPool;
use Magento\Framework\EntityManager\TypeResolver;

final class EntityIterator implements EntityIteratorInterface
{
    private HydratorPool $hydratorPool;

    private TypeResolver $typeResolver;

    private EntityValueProcessorInterface $processor;

    public function __construct(
        HydratorPool $hydratorPool,
        TypeResolver $typeResolver,
        EntityValueProcessorInterface $processor
    ) {
        $this->hydratorPool = $hydratorPool;
        $this->typeResolver = $typeResolver;
        $this->processor = $processor;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function iterate(object $entity): void
    {
        $values = $this->hydratorPool->getHydrator($this->typeResolver->resolve($entity))->extract($entity);

        foreach ($values as $key => $value) {
            $this->processor->process($key, $value);
        }
    }
}
