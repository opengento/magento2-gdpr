<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Processor\Utils;

use Magento\Framework\ObjectManager\TMap;

/**
 * Class CompositeDataFilterProcessor
 */
final class CompositeDataFilterProcessor implements DataFilterProcessorInterface
{
    /**
     * @var \Magento\Framework\ObjectManager\TMap
     */
    private $processorPool;

    /**
     * @param \Magento\Framework\ObjectManager\TMap $processorPool
     */
    public function __construct(
        TMap $processorPool
    ) {
        $this->processorPool = $processorPool;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(array $scheme, array $data): array
    {
        $result = [];

        /** @var \Opengento\Gdpr\Service\Export\Processor\Utils\DataFilterProcessorInterface $processor */
        foreach ($this->processorPool->getIterator() as $processor) {
            $result[] = $processor->execute($scheme, $data);
        }

        return \array_merge(...$result);
    }
}
