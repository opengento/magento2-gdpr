<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Anonymizer;

use Magento\Framework\EntityManager\HydratorPool;
use Magento\Framework\EntityManager\TypeResolver;
use Opengento\Gdpr\Model\Entity\DataCollectorPool;
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;

/**
 * Class Entity
 */
class Entity implements AnonymizerInterface
{
    /**
     * @var \Opengento\Gdpr\Model\Entity\DataCollectorPool
     */
    private $dataCollectorPool;

    /**
     * @var \Magento\Framework\EntityManager\TypeResolver
     */
    private $typeResolver;

    /**
     * @var \Magento\Framework\EntityManager\HydratorPool
     */
    private $hydratorPool;

    /**
     * @param \Opengento\Gdpr\Model\Entity\DataCollectorPool $dataCollectorPool
     * @param \Magento\Framework\EntityManager\TypeResolver $typeResolver
     * @param \Magento\Framework\EntityManager\HydratorPool $hydratorPool
     */
    public function __construct(
        DataCollectorPool $dataCollectorPool,
        TypeResolver $typeResolver,
        HydratorPool $hydratorPool
    ) {
        $this->dataCollectorPool = $dataCollectorPool;
        $this->typeResolver = $typeResolver;
        $this->hydratorPool = $hydratorPool;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function anonymize($entity)
    {
        if (!\is_object($entity)) {
            throw new \InvalidArgumentException(
                \sprintf('Argument "$entity" must be an object, type "%s" given.', \gettype($entity))
            );
        }

        $entityType = $this->typeResolver->resolve($entity);
        $hydrator = $this->hydratorPool->getHydrator($entityType);
        $dataCollector = $this->dataCollectorPool->getDataCollector($entityType);
        $hydrator->hydrate($entity, $dataCollector->collect($entity));

        return $entity;
    }
}
