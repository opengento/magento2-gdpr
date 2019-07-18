<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity;

use Magento\Framework\EntityManager\HydratorPool;
use Magento\Framework\EntityManager\TypeResolver;

/**
 * Class EntityIterator
 */
final class EntityIterator implements EntityIteratorInterface
{
    /**
     * @var \Magento\Framework\EntityManager\HydratorPool
     */
    private $hydratorPool;

    /**
     * @var \Magento\Framework\EntityManager\TypeResolver
     */
    private $typeResolver;

    /**
     * @var \Opengento\Gdpr\Model\Entity\EntityValueProcessorInterface
     */
    private $processor;

    /**
     * @param \Magento\Framework\EntityManager\HydratorPool $hydratorPool
     * @param \Magento\Framework\EntityManager\TypeResolver $typeResolver
     * @param \Opengento\Gdpr\Model\Entity\EntityValueProcessorInterface $processor
     */
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
     * @throws \Exception
     */
    public function iterate($entity): void
    {
        $values = $this->hydratorPool->getHydrator($this->typeResolver->resolve($entity))->extract($entity);

        foreach ($values as $key => $value) {
            $this->processor->process($entity, $key, $value);
        }
    }
}
