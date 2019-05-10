<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Processor\Entity\EntityValue;

use Opengento\Gdpr\Model\Entity\DocumentInterface;
use Opengento\Gdpr\Model\Entity\EntityValueProcessorInterface;
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;
use Opengento\Gdpr\Service\Anonymize\AnonymizerPool;
use Opengento\Gdpr\Service\Anonymize\MetadataInterface;

/**
 * Class SmartProcessor
 */
final class SmartProcessor implements EntityValueProcessorInterface
{
    /**
     * @var \Opengento\Gdpr\Model\Entity\DocumentInterface
     */
    public $document;

    /**
     * @var \Opengento\Gdpr\Service\Anonymize\MetadataInterface
     */
    private $metadata;

    /**
     * @var \Opengento\Gdpr\Service\Anonymize\AnonymizerPool
     */
    private $anonymizerPool;

    /**
     * @param \Opengento\Gdpr\Model\Entity\DocumentInterface $document
     * @param \Opengento\Gdpr\Service\Anonymize\MetadataInterface $metadata
     * @param \Opengento\Gdpr\Service\Anonymize\AnonymizerPool $anonymizerPool
     */
    public function __construct(
        DocumentInterface $document,
        MetadataInterface $metadata,
        AnonymizerPool $anonymizerPool
    ) {
        $this->document = $document;
        $this->metadata = $metadata;
        $this->anonymizerPool = $anonymizerPool;
    }

    /**
     * @inheritdoc
     */
    public function process($entity, string $key, $value): void
    {
        if (\in_array($key, $this->metadata->getAttributes(), true)) {
            $this->document->addData($key, $this->resolveAnonymizer($key)->anonymize($value));
        }
    }

    /**
     * Resolve the the anonymizer code by attribute code
     *
     * @param string $key
     * @return \Opengento\Gdpr\Service\Anonymize\AnonymizerInterface
     */
    private function resolveAnonymizer(string $key): AnonymizerInterface
    {
        return $this->anonymizerPool->getAnonymizer(
            $this->metadata->getAnonymizerStrategiesByAttributes()[$key] ?? AnonymizerPool::DEFAULT_ANONYMIZER
        );
    }
}
