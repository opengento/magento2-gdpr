<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity;

class DataCollector implements DataCollectorInterface
{
    /**
     * @var EntityIteratorInterface
     */
    private EntityIteratorInterface $entityIterator;

    /**
     * @var DocumentInterface
     */
    private DocumentInterface $document;

    public function __construct(
        EntityIteratorInterface $entityIterator,
        DocumentInterface $document
    ) {
        $this->entityIterator = $entityIterator;
        $this->document = $document;
    }

    public function collect(object $entity): array
    {
        $this->entityIterator->iterate($entity);
        $data = $this->document->getData();
        $this->document->setData([]);

        return $data;
    }
}
