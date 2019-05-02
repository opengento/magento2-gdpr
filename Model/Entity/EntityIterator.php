<?php
/**
 * Copyright © 2019 Opengento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity;

use Magento\Framework\EntityManager\HydratorPool;

/**
 * Class EntityIterator
 */
class EntityIterator implements EntityIteratorInterface
{
    /**
     * @var \Magento\Framework\EntityManager\HydratorPool
     */
    private $hydratorPool;

    /**
     * @var \Opengento\Gdpr\Model\Entity\EntityValueProcessorInterface
     */
    private $processor;

    /**
     * @param \Magento\Framework\EntityManager\HydratorPool $hydratorPool
     * @param \Opengento\Gdpr\Model\Entity\EntityValueProcessorInterface $processor
     */
    public function __construct(
        HydratorPool $hydratorPool,
        EntityValueProcessorInterface $processor
    ) {
        $this->hydratorPool = $hydratorPool;
        $this->processor = $processor;
    }

    /**
     * @inheritdoc
     */
    public function iterate($entity): void
    {
        foreach ($this->hydratorPool->getHydrator($entity)->extract($entity) as $key => $value) {
            $this->processor->process($entity, $key, $value);
        }
    }
}
