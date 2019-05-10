<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity;

/**
 * Class DataCollectorPool
 */
final class DataCollectorPool
{
    /**
     * @var \Opengento\Gdpr\Model\Entity\DataCollectorInterface[]
     */
    private $dataCollectors;

    /**
     * @param \Opengento\Gdpr\Model\Entity\DataCollectorInterface[] $dataCollectors
     */
    public function __construct(
        array $dataCollectors
    ) {
        $this->dataCollectors = (static function (DataCollectorInterface ...$dataCollectors) {
            return $dataCollectors;
        })(...\array_values($dataCollectors));

        $this->dataCollectors = \array_combine(\array_keys($dataCollectors), $this->dataCollectors);
    }

    /**
     * Retrieve the data collector by its entity type
     *
     * @param string $entityType
     * @return \Opengento\Gdpr\Model\Entity\DataCollectorInterface
     */
    public function getDataCollector(string $entityType): DataCollectorInterface
    {
        if (!isset($this->dataCollectors[$entityType])) {
            throw new \LogicException(
                \sprintf('There is no registered data collector for the entity type "%s".', $entityType)
            );
        }

        return $this->dataCollectors[$entityType];
    }
}
