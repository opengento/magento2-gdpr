<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Action;

use Opengento\Gdpr\Api\Data\ActionEntityInterface;
use function array_reduce;
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
        return array_reduce(
            $this->processors,
            static function (ProcessorInterface $processor) use ($actionEntity): array {
                return $processor->execute($actionEntity);
            },
            []
        );
    }
}
