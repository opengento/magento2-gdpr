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

final class ProcessorResolverStrategy implements ProcessorResolverInterface
{
    /**
     * @var ProcessorResolverFactory
     */
    private ProcessorResolverFactory $resolverFactory;

    private MetadataInterface $metadata;

    public function __construct(
        ProcessorResolverFactory $resolverFactory,
        MetadataInterface $metadata
    ) {
        $this->resolverFactory = $resolverFactory;
        $this->metadata = $metadata;
    }

    public function resolve(string $component): ProcessorInterface
    {
        return $this->resolverFactory->get($this->metadata->getComponentProcessor($component))->resolve($component);
    }
}
