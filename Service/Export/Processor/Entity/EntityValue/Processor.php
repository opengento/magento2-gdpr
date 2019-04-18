<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Processor\Entity\EntityValue;

use Opengento\Gdpr\Model\Entity\EntityValueProcessorInterface;
use Opengento\Gdpr\Service\Export\Processor\Entity\ConfigInterface;
use Opengento\Gdpr\Service\Export\Processor\Entity\DocumentInterface;

/**
 * Class Processor
 */
final class Processor implements EntityValueProcessorInterface
{
    /**
     * @var \Opengento\Gdpr\Service\Export\Processor\Entity\DocumentInterface
     */
    public $document;

    /**
     * @var \Opengento\Gdpr\Service\Export\Processor\Entity\ConfigInterface
     */
    private $config;

    /**
     * @param \Opengento\Gdpr\Service\Export\Processor\Entity\DocumentInterface $document
     * @param \Opengento\Gdpr\Service\Export\Processor\Entity\ConfigInterface $config
     */
    public function __construct(
        DocumentInterface $document,
        ConfigInterface $config
    ) {
        $this->document = $document;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function process($entity, string $key, $value): void
    {
        if (\in_array($key, $this->config->getAttributes(), true)) {
            $this->document->addData($key, $value);
        }
    }
}
