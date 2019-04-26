<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Processor\Entity;

use Opengento\Gdpr\Model\Entity\EntityIteratorInterface;

/**
 * Class DataCollector
 */
final class DataCollector implements DataCollectorInterface
{
    /**
     * @var \Opengento\Gdpr\Model\Entity\EntityIteratorInterface
     */
    private $entityIterator;

    /**
     * @var \Opengento\Gdpr\Service\Export\Processor\Entity\DocumentInterface
     */
    private $document;

    /**
     * @param \Opengento\Gdpr\Model\Entity\EntityIteratorInterface $entityIterator
     * @param \Opengento\Gdpr\Service\Export\Processor\Entity\DocumentInterface $document
     */
    public function __construct(
        EntityIteratorInterface $entityIterator,
        DocumentInterface $document
    ) {
        $this->entityIterator = $entityIterator;
        $this->document = $document;
    }

    /**
     * @inheritdoc
     */
    public function collect($entity): array
    {
        $this->entityIterator->iterate($entity);

        return $this->document->getData();
    }
}
