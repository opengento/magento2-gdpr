<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity;

final class DataCollector implements DataCollectorInterface
{
    /**
     * @var EntityIteratorInterface
     */
    private $entityIterator;

    /**
     * @var DocumentInterface
     */
    private $document;

    public function __construct(
        EntityIteratorInterface $entityIterator,
        DocumentInterface $document
    ) {
        $this->entityIterator = $entityIterator;
        $this->document = $document;
    }

    public function collect($entity): array
    {
        $this->entityIterator->iterate($entity);
        $data = $this->document->getData();
        $this->document->setData([]);

        return  $data;
    }
}
