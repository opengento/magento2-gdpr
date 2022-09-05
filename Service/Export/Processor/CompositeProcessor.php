<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Processor;

use Opengento\Gdpr\Service\Export\ProcessorInterface;
use function array_reduce;

final class CompositeProcessor implements ProcessorInterface
{
    /**
     * @var ProcessorInterface[]
     */
    private array $processors;

    public function __construct(
        array $processors
    ) {
        $this->processors = $processors;
    }

    public function execute(int $entityId, array $data): array
    {
        return array_reduce(
            $this->processors,
            static function (array $data, ProcessorInterface $processor) use ($entityId): array {
                return $processor->execute($entityId, $data);
            },
            $data
        );
    }
}
