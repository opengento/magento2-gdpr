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

/**
 * Class DataProcessor
 */
final class DataProcessor implements EntityValueProcessorInterface
{
    /**
     * @var \Opengento\Gdpr\Model\Entity\DocumentInterface
     */
    public $document;

    /**
     * @var \Opengento\Gdpr\Model\Entity\MetadataInterface
     */
    private $metadata;

    /**
     * @param \Opengento\Gdpr\Model\Entity\DocumentInterface $document
     * @param \Opengento\Gdpr\Model\Entity\MetadataInterface $metadata
     */
    public function __construct(
        DocumentInterface $document,
        MetadataInterface $metadata
    ) {
        $this->document = $document;
        $this->metadata = $metadata;
    }

    /**
     * @inheritdoc
     */
    public function process($entity, string $key, $value): void
    {
        if (\in_array($key, $this->metadata->getAttributes(), true)) {
            $this->document->addData($key, $value);
        }
    }
}
