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
use function array_map;
use function array_merge;

class EraseComponents implements OptionSourceInterface
{
    private ?array $options = null;

    public function __construct(
        private ConfigInterface $objectManagerConfig,
        private string $factoryClassName
    ) {}

    public function toOptionArray(): array
    {
        return $this->options ??= array_map(
            static fn (string $delegateProcessor): array => [
                'value' => $delegateProcessor,
                'label' => new Phrase($delegateProcessor)
            ],
            $this->retrieveDelegateProcessors()
        );
    }

    /**
     * @return string[]
     */
    private function retrieveDelegateProcessors(): array
    {
        return array_keys(
            array_merge(
                [],
                ...array_map(
                    fn (string $resolver): mixed => $this->retrieveArgument($resolver, 'processors'),
                    $this->retrieveArgument($this->factoryClassName, 'processorResolvers', [])
                )
            )
        );
    }

    private function retrieveArgument(string $className, string $argumentName, mixed $defaultValue = null): mixed
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
