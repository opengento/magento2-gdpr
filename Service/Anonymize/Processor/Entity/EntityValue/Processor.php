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
use function in_array;

final class Processor implements EntityValueProcessorInterface
{
    /**
     * @var DocumentInterface
     */
    private $document;

    /**
     * @var MetadataInterface
     */
    private $metadata;

    /**
     * @var AnonymizerInterface
     */
    private $anonymizer;

    public function __construct(
        DocumentInterface $document,
        MetadataInterface $metadata,
        AnonymizerInterface $anonymizer
    ) {
        $this->document = $document;
        $this->metadata = $metadata;
        $this->anonymizer = $anonymizer;
    }

    public function process(object $entity, string $key, $value): void
    {
        if (in_array($key, $this->metadata->getAttributes(), true)) {
            $this->document->addData($key, $this->anonymizer->anonymize($value));
        }
    }
}
