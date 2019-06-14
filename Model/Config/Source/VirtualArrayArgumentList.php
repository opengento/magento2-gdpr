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

/**
 * Class VirtualArrayArgumentList
 */
final class VirtualArrayArgumentList implements OptionSourceInterface
{
    /**
     * @var \Magento\Framework\ObjectManager\ConfigInterface
     */
    private $objectManagerConfig;

    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $argumentName;

    /**
     * @var array
     */
    private $options;

    /**
     * @param \Magento\Framework\ObjectManager\ConfigInterface $objectManagerConfig
     * @param string $className
     * @param string $argumentName
     */
    public function __construct(
        ConfigInterface $objectManagerConfig,
        string $className,
        string $argumentName
    ) {
        $this->objectManagerConfig = $objectManagerConfig;
        $this->className = $className;
        $this->argumentName = $argumentName;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        if (!$this->options) {
            foreach (\array_keys($this->retrieveItems()) as $item) {
                $this->options[] = ['value' => $item, 'label' => new Phrase($item)];
            }
        }

        return $this->options;
    }

    /**
     * Retrieve the items from the di settings
     *
     * @return string[]
     */
    private function retrieveItems(): array
    {
        $arguments = $this->objectManagerConfig->getArguments(
            $this->objectManagerConfig->getPreference($this->className)
        );

        return $arguments[$this->argumentName]['_v_'] ?? $arguments[$this->argumentName] ?? [];
    }
}
