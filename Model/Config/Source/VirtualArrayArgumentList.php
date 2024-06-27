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

class VirtualArrayArgumentList implements OptionSourceInterface
{
    private ?array $options = null;

    public function __construct(
        private ConfigInterface $objectManagerConfig,
        private string $className,
        private string $argumentName
    ) {}

    public function toOptionArray(): array
    {
        return $this->options ??= array_map(
            static fn (string $item): array => ['value' => $item, 'label' => new Phrase($item)],
            array_keys($this->retrieveItems())
        );
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
