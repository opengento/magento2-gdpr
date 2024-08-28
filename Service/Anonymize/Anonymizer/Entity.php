<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Anonymizer;

use Exception;
use InvalidArgumentException;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\EntityManager\HydratorPool;
use Magento\Framework\EntityManager\TypeResolver;
use Opengento\Gdpr\Model\Entity\DataCollectorInterface;
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;

use function gettype;
use function is_object;
use function sprintf;

class Entity implements AnonymizerInterface
{
    public function __construct(
        private DataCollectorInterface $dataCollector,
        private TypeResolver $typeResolver,
        private HydratorPool $hydratorPool
    ) {}

    /**
     * @throws Exception
     */
    public function anonymize($value): object
    {
        if (!is_object($value)) {
            throw new InvalidArgumentException(
                sprintf('Argument "$entity" must be an object, type "%s" given.', gettype($value))
            );
        }

        return $this->resolveHydrator($value)->hydrate($value, $this->dataCollector->collect($value));
    }

    /**
     * @throws Exception
     */
    private function resolveHydrator(object $entity): HydratorInterface
    {
        return $this->hydratorPool->getHydrator($this->typeResolver->resolve($entity));
    }
}
