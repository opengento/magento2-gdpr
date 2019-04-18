<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Processor\Entity\EntityValue;

use Opengento\Gdpr\Model\Entity\EntityValueProcessorInterface;
use Opengento\Gdpr\Service\Anonymize\Processor\Entity\ConfigInterface;

/**
 * Class Processor
 */
final class Processor implements EntityValueProcessorInterface
{
    /**
     * @var \Opengento\Gdpr\Service\Anonymize\Processor\Entity\ConfigInterface
     */
    private $config;

    /**
     * @param \Opengento\Gdpr\Service\Anonymize\Processor\Entity\ConfigInterface $config
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
        if (\in_array($key, $this->config->getAttributes(), true)) {
            // todo anonymize value and push it in the entity object
        }
    }
}
