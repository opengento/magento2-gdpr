<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Erase\Processor;

use Opengento\Gdpr\Service\Erase\MetadataInterface;
use Opengento\Gdpr\Service\Erase\ProcessorFactory;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;

/**
 * Class ProcessorStrategy
 */
final class ProcessorStrategy implements ProcessorInterface
{
    /**
     * @var \Opengento\Gdpr\Service\Erase\ProcessorFactory
     */
    private $processorFactory;

    /**
     * @var \Opengento\Gdpr\Service\Erase\MetadataInterface
     */
    private $metadata;

    /**
     * @param \Opengento\Gdpr\Service\Erase\ProcessorFactory $processorFactory
     * @param \Opengento\Gdpr\Service\Erase\MetadataInterface $metadata
     */
    public function __construct(
        ProcessorFactory $processorFactory,
        MetadataInterface $metadata
    ) {
        $this->processorFactory = $processorFactory;
        $this->metadata = $metadata;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $component, int $customerId): bool
    {
        $processor = $this->processorFactory->get($this->metadata->getComponentProcessor($component));

        return $processor->execute($component, $customerId);
    }
}
