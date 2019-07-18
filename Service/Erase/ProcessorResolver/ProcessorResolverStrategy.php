<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Erase\ProcessorResolver;

use Opengento\Gdpr\Service\Erase\MetadataInterface;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;
use Opengento\Gdpr\Service\Erase\ProcessorResolverFactory;
use Opengento\Gdpr\Service\Erase\ProcessorResolverInterface;

/**
 * Class ProcessorResolverStrategy
 */
final class ProcessorResolverStrategy implements ProcessorResolverInterface
{
    /**
     * @var \Opengento\Gdpr\Service\Erase\ProcessorResolverFactory
     */
    private $processorResolverFactory;

    /**
     * @var \Opengento\Gdpr\Service\Erase\MetadataInterface
     */
    private $metadata;

    /**
     * @param \Opengento\Gdpr\Service\Erase\ProcessorResolverFactory $processorResolverFactory
     * @param \Opengento\Gdpr\Service\Erase\MetadataInterface $metadata
     */
    public function __construct(
        ProcessorResolverFactory $processorResolverFactory,
        MetadataInterface $metadata
    ) {
        $this->processorResolverFactory = $processorResolverFactory;
        $this->metadata = $metadata;
    }

    /**
     * @inheritdoc
     */
    public function resolve(string $component): ProcessorInterface
    {
        return $this->processorResolverFactory->get($this->metadata->getComponentProcessor($component))->resolve($component);
    }
}
