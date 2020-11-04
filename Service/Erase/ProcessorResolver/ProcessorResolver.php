<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Erase\ProcessorResolver;

use InvalidArgumentException;
use Magento\Framework\ObjectManagerInterface;
use Opengento\Gdpr\Service\Erase\ProcessorInterface;
use Opengento\Gdpr\Service\Erase\ProcessorResolverInterface;
use function sprintf;

final class ProcessorResolver implements ProcessorResolverInterface
{
    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        array $processors,
        ObjectManagerInterface $objectManager
    ) {
        $this->processors = $processors;
        $this->objectManager = $objectManager;
    }

    public function resolve(string $component): ProcessorInterface
    {
        if (!isset($this->processors[$component])) {
            throw new InvalidArgumentException(sprintf('Unknown processor type "%s".', $component));
        }

        return $this->objectManager->get($this->processors[$component]);
    }
}
