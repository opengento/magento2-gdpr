<?php
/**
 * Copyright © 2019 Opengento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity\EntityValue;

use Opengento\Gdpr\Model\Entity\EntityValueProcessorInterface;

/**
 * Class DefaultProcessor
 */
class DefaultProcessor implements EntityValueProcessorInterface
{
    /**
     * @var \Opengento\Gdpr\Model\Entity\EntityValueProcessorInterface
     */
    private $processor;

    /**
     * @param \Opengento\Gdpr\Model\Entity\EntityValueProcessorInterface $processor
     */
    public function __construct(
        EntityValueProcessorInterface $processor
    ) {
        $this->processor = $processor;
    }

    /**
     * @inheritdoc
     */
    public function process($entity, string $key, $value): void
    {
        $this->processor->process($entity, $key, $value);
    }
}
