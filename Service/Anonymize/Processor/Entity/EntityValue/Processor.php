<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Processor\Entity\EntityValue;

use Opengento\Gdpr\Model\Entity\DocumentInterface;
use Opengento\Gdpr\Model\Entity\EntityValueProcessorInterface;
use Opengento\Gdpr\Model\Entity\MetadataInterface;
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;

/**
 * Class Processor
 */
final class Processor implements EntityValueProcessorInterface
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
     * @var \Opengento\Gdpr\Service\Anonymize\AnonymizerInterface
     */
    private $anonymizer;

    /**
     * @param \Opengento\Gdpr\Model\Entity\DocumentInterface $document
     * @param \Opengento\Gdpr\Model\Entity\MetadataInterface $metadata
     * @param \Opengento\Gdpr\Service\Anonymize\AnonymizerInterface $anonymizer
     */
    public function __construct(
        DocumentInterface $document,
        MetadataInterface $metadata,
        AnonymizerInterface $anonymizer
    ) {
        $this->document = $document;
        $this->metadata = $metadata;
        $this->anonymizer = $anonymizer;
    }

    /**
     * @inheritdoc
     */
    public function process($entity, string $key, $value): void
    {
        if (\in_array($key, $this->metadata->getAttributes(), true)) {
            $this->document->addData($key, $this->anonymizer->anonymize($value));
        }
    }
}
