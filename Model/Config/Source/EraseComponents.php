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
use function array_keys;
use function array_merge;

final class EraseComponents implements OptionSourceInterface
{
    /**
     * @var ConfigInterface
     */
    private ConfigInterface $objectManagerConfig;

    /**
     * Class must be an instance of `\Opengento\Gdpr\Service\Erase\ProcessorResolverFactory`
     *
     * @var string
     */
    private string $factoryClassName;

    /**
     * @var string[][]
     */
    private array $options = [];

    public function __construct(
        ConfigInterface $objectManagerConfig,
        string $factoryClassName
    ) {
        $this->objectManagerConfig = $objectManagerConfig;
        $this->factoryClassName = $factoryClassName;
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
        $resolvers = $this->retrieveArgument($this->factoryClassName, 'processorResolvers', []);

        foreach ($resolvers as $resolver) {
            $delegateProcessors[] = $this->retrieveArgument($resolver, 'processors');
        }

        return array_keys(array_merge([], ...$delegateProcessors));
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
