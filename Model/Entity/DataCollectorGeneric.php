<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity;

use Exception;
use LogicException;
use Magento\Framework\EntityManager\TypeResolver;
use function sprintf;

final class DataCollectorGeneric implements DataCollectorInterface
{
    private TypeResolver $typeResolver;

    /**
     * @var DataCollectorInterface[]
     */
    private array $dataCollectors;

    public function __construct(
        TypeResolver $typeResolver,
        array $dataCollectors
    ) {
        $this->typeResolver = $typeResolver;
        $this->dataCollectors = $dataCollectors;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function collect(object $entity): array
    {
        $entityType = $this->typeResolver->resolve($entity);

        if (!isset($this->dataCollectors[$entityType])) {
            throw new LogicException(
                sprintf('There is no registered data collector for the entity type "%s".', $entityType)
            );
        }

        return $this->dataCollectors[$entityType]->collect($entity);
    }
}
