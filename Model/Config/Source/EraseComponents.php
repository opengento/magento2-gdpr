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
 * Class EraseComponents
 */
final class EraseComponents implements OptionSourceInterface
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
            foreach ($this->retrieveDelegateProcessors() as $delegateProcessor) {
                $this->options[] = ['value' => $delegateProcessor, 'label' => new Phrase($delegateProcessor)];
            }
        }

        return $this->options;
    }

    /**
     * Retrieve the delegate processors
     *
     * @return string[]
     */
    private function retrieveDelegateProcessors(): array
    {
        $delegateProcessors = [];

        foreach ($this->retrieveArgument(ProcessorFactory::class, 'processors', []) as $eraseProcessor) {
            $processorPool = $this->retrieveArgument($eraseProcessor, 'processorPool');

            if ($processorPool) {
                $delegateProcessors[] = $this->retrieveArgument($processorPool, 'array', []);
            }
        }

        return \array_keys(\array_merge(...$delegateProcessors));
    }

    /**
     * Retrieve a construct argument value of a class
     *
     * @param string $className
     * @param string $argumentName
     * @param null $defaultValue
     * @return mixed
     */
    private function retrieveArgument(string $className, string $argumentName, $defaultValue = null)
    {
        $arguments = $this->objectManagerConfig->getArguments(
            $this->objectManagerConfig->getPreference($className)
        );

        // Hack: retrieve the argument even if the DI is cached, compiled or whatever...
        return $arguments[$argumentName]['_i_']
            ?? $arguments[$argumentName]['_ins_']
            ?? $arguments[$argumentName]['_v_']
            ?? $arguments[$argumentName]['_vac_']
            ?? $arguments[$argumentName]['_vn_']
            ?? $arguments[$argumentName]['_a_']
            ?? $arguments[$argumentName]['_d_']
            ?? $arguments[$argumentName]
            ?? $defaultValue;
    }
}
