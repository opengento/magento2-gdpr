<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity;

use Exception;
use Magento\Framework\EntityManager\TypeResolver;

final class DataCollectorGeneric implements DataCollectorInterface
{
    /**
     * @var TypeResolver
     */
    private $typeResolver;

    /**
     * @var DataCollectorInterface[]
     */
    private $dataCollectors;

    /**
     * @param TypeResolver $typeResolver
     * @param DataCollectorInterface[] $dataCollectors
     */
    public function __construct(
        TypeResolver $typeResolver,
        array $dataCollectors
    ) {
        $this->typeResolver = $typeResolver;
        $this->dataCollectors = (static function (DataCollectorInterface ...$dataCollectors) {
            return $dataCollectors;
        })(...\array_values($dataCollectors));

        $this->dataCollectors = \array_combine(\array_keys($dataCollectors), $this->dataCollectors);
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function collect(object $entity): array
    {
        $entityType = $this->typeResolver->resolve($entity);

        if (!isset($this->dataCollectors[$entityType])) {
            throw new \LogicException(
                \sprintf('There is no registered data collector for the entity type "%s".', $entityType)
            );
        }

        return $this->dataCollectors[$entityType]->collect($entity);
    }
}
