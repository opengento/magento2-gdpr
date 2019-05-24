<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\ObjectManager\ConfigInterface;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Service\Erase\ProcessorFactory;

/**
 * Class EraseProcessors
 */
class EraseProcessors implements OptionSourceInterface
{
    /**
     * @var \Magento\Framework\ObjectManager\ConfigInterface
     */
    private $objectManagerConfig;

    /**
     * @var string[][]
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
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        if (!$this->options) {
            foreach ($this->retrieveProcessorsNames() as $processorName) {
                $this->options[] = ['value' => $processorName, 'label' => new Phrase($processorName)];
            }
        }

        return $this->options;
    }

    /**
     * Retrieve the processors names
     *
     * @return array
     */
    private function retrieveProcessorsNames(): array
    {
        $arguments = $this->objectManagerConfig->getArguments(
            $this->objectManagerConfig->getPreference(ProcessorFactory::class)
        );

        return \array_keys($arguments['processors']['_v_'] ?? $arguments['processors'] ?? []);
    }
}
