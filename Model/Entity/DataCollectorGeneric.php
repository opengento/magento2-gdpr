<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity;

use Magento\Framework\EntityManager\TypeResolver;

/**
 * Class DataCollectorGeneric
 */
final class DataCollectorGeneric implements DataCollectorInterface
{
    /**
     * @var \Magento\Framework\EntityManager\TypeResolver
     */
    private $typeResolver;

    /**
     * @var \Opengento\Gdpr\Model\Entity\DataCollectorInterface[]
     */
    private $dataCollectors;

    /**
     * @param \Magento\Framework\EntityManager\TypeResolver $typeResolver
     * @param array $dataCollectors
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
     * @throws \Exception
     */
    public function collect($entity): array
    {
        $entityType = $this->typeResolver->resolve($entity);

        if (!isset($this->dataCollectors[$entityType])) {
            throw new \LogicException(
                \sprintf('There is no registered data collector for the entity type "%s".', $entityType)
            );
        }

        return $this->dataCollectors[$entityType];
    }
}
