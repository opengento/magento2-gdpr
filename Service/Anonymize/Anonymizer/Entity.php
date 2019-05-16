<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Anonymizer;

use Magento\Framework\EntityManager\HydratorPool;
use Magento\Framework\EntityManager\TypeResolver;
use Opengento\Gdpr\Model\Entity\DataCollectorInterface;
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;

/**
 * Class Entity
 */
class Entity implements AnonymizerInterface
{
    /**
     * @var \Opengento\Gdpr\Model\Entity\DataCollectorInterface
     */
    private $dataCollector;

    /**
     * @var \Magento\Framework\EntityManager\TypeResolver
     */
    private $typeResolver;

    /**
     * @var \Magento\Framework\EntityManager\HydratorPool
     */
    private $hydratorPool;

    /**
     * @param \Opengento\Gdpr\Model\Entity\DataCollectorInterface $dataCollector
     * @param \Magento\Framework\EntityManager\TypeResolver $typeResolver
     * @param \Magento\Framework\EntityManager\HydratorPool $hydratorPool
     */
    public function __construct(
        DataCollectorInterface $dataCollector,
        TypeResolver $typeResolver,
        HydratorPool $hydratorPool
    ) {
        $this->dataCollector = $dataCollector;
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

        $hydrator = $this->hydratorPool->getHydrator($this->typeResolver->resolve($entity));
        $hydrator->hydrate($entity, $this->dataCollector->collect($entity));

        return $entity;
    }
}
