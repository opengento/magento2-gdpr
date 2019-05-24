<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Processor;

use Opengento\Gdpr\Service\Export\ProcessorInterface;

/**
 * Class CompositeProcessor
 */
final class CompositeProcessor implements ProcessorInterface
{
    /**
     * @var \Opengento\Gdpr\Service\Export\ProcessorInterface[]
     */
    private $processors;

    /**
     * @param \Opengento\Gdpr\Service\Export\ProcessorInterface[] $processors
     */
    public function __construct(
        array $processors
    ) {
        $this->processors = (static function (ProcessorInterface ...$processors): array {
            return $processors;
        })(...\array_values($processors));

        $this->processors = \array_combine(\array_keys($processors), $this->processors);
    }

    /**
     * @inheritdoc
     */
    public function execute(int $customerId, array $data): array
    {
        return \array_reduce(
            $this->processors,
            static function (array $data, ProcessorInterface $processor) use ($customerId) {
                return $processor->execute($customerId, $data);
            },
            $data
        );
    }
}
