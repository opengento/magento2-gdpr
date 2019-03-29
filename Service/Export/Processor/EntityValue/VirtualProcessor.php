<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Processor\EntityValue;

use Opengento\Gdpr\Model\Entity\EntityValueProcessorInterface;
use Opengento\Gdpr\Service\Export\ConfigInterface;

/**
 * Class VirtualProcessor
 */
class VirtualProcessor implements EntityValueProcessorInterface
{
    /**
     * @var \Opengento\Gdpr\Service\Export\ConfigInterface
     */
    private $config;

    /**
     * @param \Opengento\Gdpr\Service\Export\ConfigInterface $config
     */
    public function __construct(
        ConfigInterface $config
    ) {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function process($entity, string $key, $value): void
    {
        if (\in_array($key, $this->config->getAttributes())) {
            // todo Add value to the Container of DocumentInterface from the export service
        }
    }
}
