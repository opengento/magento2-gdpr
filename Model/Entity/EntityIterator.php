<?php
/**
 * Copyright © 2019 Opengento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity;

use Magento\Framework\EntityManager\HydratorInterface;

/**
 * Class EntityIterator
 */
class EntityIterator implements EntityIteratorInterface
{
    /**
     * @var \Magento\Framework\EntityManager\HydratorInterface
     */
    private $hydrator;

    /**
     * @var \Opengento\Gdpr\Model\Entity\EntityValueProcessorInterface
     */
    private $processor;

    /**
     * @param \Magento\Framework\EntityManager\HydratorInterface $hydrator
     * @param \Opengento\Gdpr\Model\Entity\EntityValueProcessorInterface $processor
     */
    public function __construct(
        HydratorInterface $hydrator,
        EntityValueProcessorInterface $processor
    ) {
        $this->hydrator = $hydrator;
        $this->processor = $processor;
    }

    /**
     * {@inheritdoc}
     */
    public function iterate($entity): void
    {
        foreach ($this->hydrator->extract($entity) as $key => $value) {
            $this->processor->process($entity, $key, $value);
        }
    }
}
