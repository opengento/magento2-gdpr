<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Erase\Processor;

use Opengento\Gdpr\Model\Config\Source\EraseComponents;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;
use Opengento\Gdpr\Service\Erase\ProcessorResolverInterface;

/**
 * Class EraseProcessor
 */
final class EraseProcessor implements ProcessorInterface
{
    /**
     * @var \Opengento\Gdpr\Service\Erase\ProcessorResolverInterface
     */
    private $eraseProcessorResolver;

    /**
     * @var \Opengento\Gdpr\Model\Config\Source\EraseComponents
     */
    private $eraseComponents;

    /**
     * @param \Opengento\Gdpr\Service\Erase\ProcessorResolverInterface $eraseProcessorResolver
     * @param \Opengento\Gdpr\Model\Config\Source\EraseComponents $eraseComponents
     */
    public function __construct(
        ProcessorResolverInterface $eraseProcessorResolver,
        EraseComponents $eraseComponents
    ) {
        $this->eraseProcessorResolver = $eraseProcessorResolver;
        $this->eraseComponents = $eraseComponents;
    }

    /**
     * @inheritdoc
     */
    public function execute(int $entityId): bool
    {
        foreach (\array_column($this->eraseComponents->toOptionArray(), 'value') as $component) {
            $processor = $this->eraseProcessorResolver->resolve($component);
            if (!$processor->execute($entityId)) {
                return false;
            }
        }

        return true;
    }
}
