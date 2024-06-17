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

use function array_column;

class EraseProcessor implements ProcessorInterface
{
    /**
     * @var ProcessorResolverInterface
     */
    private ProcessorResolverInterface $processorResolver;

    /**
     * @var EraseComponents
     */
    private EraseComponents $eraseComponents;

    public function __construct(
        ProcessorResolverInterface $processorResolver,
        EraseComponents $eraseComponents
    ) {
        $this->processorResolver = $processorResolver;
        $this->eraseComponents = $eraseComponents;
    }

    public function execute(int $entityId): bool
    {
        $components = array_column($this->eraseComponents->toOptionArray(), 'value');

        foreach ($components as $component) {
            $processor = $this->processorResolver->resolve($component);
            if (!$processor->execute($entityId)) {
                return false;
            }
        }

        return true;
    }
}
