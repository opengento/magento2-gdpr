<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManager\ConfigInterface;
use Opengento\Gdpr\Model\Config;
use Opengento\Gdpr\Service\ErasureStrategy;

/**
 * Erasure Component Strategy Config Resolver
 */
class ErasureComponentStrategy
{
    /**
     * @var \Magento\Framework\ObjectManager\ConfigInterface
     */
    private $objectManagerConfig;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var array
     */
    private $componentsStrategies;

    /**
     * @param \Magento\Framework\ObjectManager\ConfigInterface $objectManagerConfig
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param string[] $componentsStrategies
     */
    public function __construct(
        ConfigInterface $objectManagerConfig,
        ScopeConfigInterface $scopeConfig,
        array $componentsStrategies = []
    ) {
        $this->objectManagerConfig = $objectManagerConfig;
        $this->scopeConfig = $scopeConfig;
        $this->initComponentsStrategies($componentsStrategies);
    }

    /**
     * Retrieve the component type strategy code
     *
     * @param string $component
     * @return string
     */
    public function getComponentStrategy(string $component): string
    {
        foreach ($this->componentsStrategies as $strategy => $componentsStrategy) {
            if (in_array($component, $componentsStrategy)) {
                return $strategy;
            }
        }

        throw new \InvalidArgumentException(sprintf('Unknown component name "%s".', $component));
    }

    /**
     * Retrieve the list of components names by strategy
     *
     * @param string $strategy
     * @return array
     */
    public function getComponentsByStrategy(string $strategy): array
    {
        return $this->componentsStrategies[$strategy] ?? [];
    }

    /**
     * Retrieve the anonymize components names
     *
     * @return string[]
     */
    public function getAnonymizeComponentsNames(): array
    {
        return array_keys($this->retrieveProcessorNames('Opengento\Gdpr\Service\Anonymize\ProcessorPool'));
    }

    /**
     * Retrieve the delete components names
     * 
     * @return string[]
     */
    public function getDeleteComponentsNames(): array
    {
        return array_keys($this->retrieveProcessorNames('Opengento\Gdpr\Service\Delete\ProcessorPool'));
    }

    /**
     * Load and retrieve the full list of components by strategies
     *
     * @return array
     */
    private function loadComponentsStrategies(): array
    {
        $anonymizeProcessor = $this->getAnonymizeComponentsNames();
        $deleteProcessor = $this->getDeleteComponentsNames();
        $configuredProcessor = $this->scopeConfig->getValue(Config::CONFIG_PATH_ERASURE_STRATEGY_COMPONENTS);
        $missingProcessor = array_diff(array_intersect($anonymizeProcessor, $deleteProcessor), $configuredProcessor);

        return [
            ErasureStrategy::STRATEGY_ANONYMIZE => array_merge(
                array_diff($anonymizeProcessor, $configuredProcessor),
                $missingProcessor
            ),
            ErasureStrategy::STRATEGY_DELETE => array_merge(
                array_diff($deleteProcessor, $missingProcessor),
                $configuredProcessor
            ),
        ];
    }

    /**
     * Initialize the components with their strategies
     *
     * @param string[] $components
     * @return void
     */
    private function initComponentsStrategies(array $components)
    {
        $this->componentsStrategies = [
            ErasureStrategy::STRATEGY_ANONYMIZE => [],
            ErasureStrategy::STRATEGY_DELETE => [],
        ];

        foreach ($components as $component => $strategy) {
            if (!isset($this->componentsStrategies[$strategy])) {
                $this->componentsStrategies[$strategy] = [];
            }
            try {
                $strategy = $this->getComponentStrategy($component);

                throw new \InvalidArgumentException(
                    sprintf('Strategy is already set for the component name "%s".', $component)
                );
            } catch (\InvalidArgumentException $e) {
                $this->componentsStrategies[$strategy][] = $component;
            }
        }

        $this->componentsStrategies = array_merge_recursive(
            $this->componentsStrategies,
            $this->loadComponentsStrategies()
        );
    }

    /**
     * Retrieve the processors names from the di settings
     *
     * @param string $processorPool
     * @return string[]
     */
    private function retrieveProcessorNames(string $processorPool): array
    {
        $processors = [];
        $typePreference = $this->objectManagerConfig->getPreference($processorPool);
        $arguments = $this->objectManagerConfig->getArguments($typePreference);

        if (isset($arguments['array'])) {
            // Workaround for compiled mode.
            $processors = isset($arguments['array']['_v_']) ? $arguments['array']['_v_'] : $arguments['array'];
        }

        return $processors;
    }
}
