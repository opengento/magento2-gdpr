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
use Opengento\Gdpr\Service\Anonymize\MetadataInterface;
use function in_array;

final class SmartProcessor implements EntityValueProcessorInterface
{
    /**
     * @var DocumentInterface
     */
    private DocumentInterface $document;

    private MetadataInterface $metadata;

    /**
     * @var AnonymizerFactory
     */
    private AnonymizerFactory $anonymizerFactory;

    public function __construct(
        DocumentInterface $document,
        MetadataInterface $metadata,
        AnonymizerFactory $anonymizerFactory
    ) {
        $this->document = $document;
        $this->metadata = $metadata;
        $this->anonymizerFactory = $anonymizerFactory;
    }

    public function process(string $key, $value): void
    {
        if (in_array($key, $this->metadata->getAttributes(), true)) {
            $this->document->addData(
                $key,
                $this->anonymizerFactory->get(
                    $this->metadata->getAnonymizerStrategiesByAttributes()[$key] ?? AnonymizerFactory::DEFAULT_KEY
                )->anonymize($value)
            );
        }
    }
}
