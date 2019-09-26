<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Processor\Entity\EntityValue;

use Opengento\Gdpr\Model\Entity\DocumentInterface;
use Opengento\Gdpr\Model\Entity\EntityValueProcessorInterface;
use Opengento\Gdpr\Model\Entity\MetadataInterface;
use function in_array;

final class DataProcessor implements EntityValueProcessorInterface
{
    /**
     * @var DocumentInterface
     */
    public $document;

    /**
     * @var MetadataInterface
     */
    private $metadata;

    public function __construct(
        DocumentInterface $document,
        MetadataInterface $metadata
    ) {
        $this->document = $document;
        $this->metadata = $metadata;
    }

    public function process($entity, string $key, $value): void
    {
        if (in_array($key, $this->metadata->getAttributes(), true)) {
            $this->document->addData($key, $value);
        }
    }
}
