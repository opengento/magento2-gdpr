<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Processor\Entity\EntityValue;

use Opengento\Gdpr\Model\Entity\DocumentInterface;
use Opengento\Gdpr\Model\Entity\EntityValueProcessorInterface;
use Opengento\Gdpr\Service\Anonymize\AnonymizerFactory;
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;
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
     * @var \Opengento\Gdpr\Service\Anonymize\AnonymizerFactory
     */
    private $anonymizerFactory;

    /**
     * @param \Opengento\Gdpr\Model\Entity\DocumentInterface $document
     * @param \Opengento\Gdpr\Service\Anonymize\MetadataInterface $metadata
     * @param \Opengento\Gdpr\Service\Anonymize\AnonymizerFactory $anonymizerFactory
     */
    public function __construct(
        DocumentInterface $document,
        MetadataInterface $metadata,
        AnonymizerFactory $anonymizerFactory
    ) {
        $this->document = $document;
        $this->metadata = $metadata;
        $this->anonymizerFactory = $anonymizerFactory;
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
        return $this->anonymizerFactory->get(
            $this->metadata->getAnonymizerStrategiesByAttributes()[$key] ?? AnonymizerFactory::DEFAULT_ANONYMIZER
        );
    }
}
