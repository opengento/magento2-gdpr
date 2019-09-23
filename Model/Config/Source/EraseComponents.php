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

final class EraseComponents implements OptionSourceInterface
{
    /**
     * @var ConfigInterface
     */
    private $objectManagerConfig;

    /**
     * Class must be an instance of `\Opengento\Gdpr\Service\Erase\ProcessorResolverFactory`
     *
     * @var string
     */
    private $processorResolverFactoryClassName;

    /**
     * @var string[][]
     */
    private $options;

    public function __construct(
        ConfigInterface $objectManagerConfig,
        string $processorResolverFactoryClassName
    ) {
        $this->objectManagerConfig = $objectManagerConfig;
        $this->processorResolverFactoryClassName = $processorResolverFactoryClassName;
    }

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
     * @return string[]
     */
    private function retrieveDelegateProcessors(): array
    {
        $delegateProcessors = [];
        /** @var string[] $resolvers */
        $resolvers = $this->retrieveArgument($this->processorResolverFactoryClassName, 'processorResolvers', []);

        foreach ($resolvers as $resolver) {
            $processorPool = $this->retrieveArgument($resolver, 'processorPool');

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
     * @param mixed $defaultValue
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
            ?? $arguments[$argumentName]['instance']
            ?? $arguments[$argumentName]['argument']
            ?? $arguments[$argumentName]
            ?? $defaultValue;
    }
}
