<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Processor;

use Opengento\Gdpr\Model\Entity\DataCollectorInterface;
use Opengento\Gdpr\Service\Export\ProcessorInterface;

abstract class AbstractDataProcessor implements ProcessorInterface
{
    private DataCollectorInterface $dataCollector;

    public function __construct(
        DataCollectorInterface $dataCollector
    ) {
        $this->dataCollector = $dataCollector;
    }

    protected function collectData(object $entity): array
    {
        return $this->dataCollector->collect($entity);
    }
}
