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

class CustomAttributesProcessor implements EntityValueProcessorInterface
{
    private EntityValueProcessorInterface $processor;

    public function __construct(
        EntityValueProcessorInterface $processor
    ) {
        $this->processor = $processor;
    }

    public function process(string $key, $values): void
    {
        if ($key === CustomAttributesDataInterface::CUSTOM_ATTRIBUTES && is_iterable($values)) {
            foreach ($values as $value) {
                $this->processor->process($key, $value);
            }
        }
    }
}
