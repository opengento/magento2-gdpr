<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Entity\EntityValue;

use Magento\Framework\Api\CustomAttributesDataInterface;
use Opengento\Gdpr\Model\Entity\EntityValueProcessorInterface;
use function is_iterable;

final class CustomAttributesProcessor implements EntityValueProcessorInterface
{
    /**
     * @var EntityValueProcessorInterface
     */
    private $processor;

    public function __construct(
        EntityValueProcessorInterface $processor
    ) {
        $this->processor = $processor;
    }

    public function process($entity, string $key, $values): void
    {
        if ($this->isValid($entity, $key, $values)) {
            foreach ($values as $value) {
                $this->processor->process($entity, $key, $value);
            }
        }
    }

    /**
     * Check whether the entity object and the value key are valid
     *
     * @param object $entity
     * @param string $key
     * @param mixed $values
     * @return bool
     */
    private function isValid($entity, string $key, $values): bool
    {
        return $entity instanceof CustomAttributesDataInterface &&
            $key === CustomAttributesDataInterface::CUSTOM_ATTRIBUTES &&
            is_iterable($values);
    }
}
