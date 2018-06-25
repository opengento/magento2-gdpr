<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\ObjectManager\ConfigInterface;
use Magento\Framework\Phrase;

/**
 * Class ExportRenderer
 */
class ExportRenderer implements OptionSourceInterface
{
    /**
     * @var \Magento\Framework\ObjectManager\ConfigInterface
     */
    private $objectManagerConfig;

    /**
     * @var array
     */
    private $options;

    /**
     * @param \Magento\Framework\ObjectManager\ConfigInterface $objectManagerConfig
     */
    public function __construct(
        ConfigInterface $objectManagerConfig
    ) {
        $this->objectManagerConfig = $objectManagerConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            foreach ($this->retrieveRenderers() as $rendererName => $renderer) {
                $this->options[] = ['label' => new Phrase($rendererName), 'value' => $rendererName];
            }
        }

        return $this->options;
    }

    /**
     * Retrieve the renderers from the di settings
     *
     * @return string[]
     */
    private function retrieveRenderers(): array
    {
        $processors = [];
        $typePreference = $this->objectManagerConfig->getPreference('Opengento\Gdpr\Service\Export\RendererPool');
        $arguments = $this->objectManagerConfig->getArguments($typePreference);

        if (isset($arguments['array'])) {
            // Workaround for compiled mode.
            $processors = isset($arguments['array']['_vac_']) ? $arguments['array']['_vac_'] : $arguments['array'];
        }

        return $processors;
    }
}
