<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity\EntityValue;

use Magento\Framework\Api\ExtensibleDataInterface;
use Opengento\Gdpr\Model\Entity\EntityValueProcessorInterface;

/**
 * Class ExtensibleDataProcessor
 */
final class ExtensibleDataProcessor implements EntityValueProcessorInterface
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
    public function process($entity, string $key, $values): void
    {
        if ($this->isValid($entity, $key, $values)) {
            foreach ($values as $value) {
                $this->processor->process($entity, $key, $value);
            }
        }
    }

    /**
     * Check wether the entity object and the value key are valid
     *
     * @param object $entity
     * @param string $key
     * @param mixed $values
     * @return bool
     */
    private function isValid($entity, string $key, $values): bool
    {
        return $entity instanceof ExtensibleDataInterface &&
            $key === ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY &&
            \is_iterable($values);
    }
}
