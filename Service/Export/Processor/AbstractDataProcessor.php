<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Processor;

use Opengento\Gdpr\Model\Entity\DataCollectorInterface;
use Opengento\Gdpr\Service\Export\ProcessorInterface;

/**
 * Class AbstractDataProcessor
 */
abstract class AbstractDataProcessor implements ProcessorInterface
{
    /**
     * @var \Opengento\Gdpr\Model\Entity\DataCollectorInterface
     */
    private $dataCollector;

    /**
     * @param \Opengento\Gdpr\Model\Entity\DataCollectorInterface $dataCollector
     */
    public function __construct(
        DataCollectorInterface $dataCollector
    ) {
        $this->dataCollector = $dataCollector;
    }

    /**
     * Collect the entity data
     *
     * @param object $entity
     * @return array
     */
    protected function collectData($entity): array
    {
        return $this->dataCollector->collect($entity);
    }
}
