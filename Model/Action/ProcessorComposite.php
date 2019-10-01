<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action;

use Opengento\Gdpr\Api\Data\ActionEntityInterface;
use function array_merge;
use function array_values;

/**
 * @api
 */
final class ProcessorComposite implements ProcessorInterface
{
    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     * @param ProcessorInterface[] $processors
     */
    public function __construct(
        array $processors
    ) {
        $this->processors = (static function(ProcessorInterface ...$processors): array {
            return $processors;
        })(...array_values($processors));
    }

    public function execute(ActionEntityInterface $actionEntity): array
    {
        $results = [];

        foreach ($this->processors as $processor) {
            $results[] = $processor->execute($actionEntity);
        }

        return array_merge(...$results);
    }
}
