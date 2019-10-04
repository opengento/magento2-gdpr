<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\Data\ActionEntityInterface;
use function array_merge;
use function compact;

final class ActionStates implements OptionSourceInterface
{
    /**
     * @var array
     */
    private $additionalOptions;

    /**
     * @var array
     */
    private $options;

    /**
     * @var array
     */
    private $optionArray;

    public function __construct(
        array $additionalOptions = []
    ) {
        $this->additionalOptions = $additionalOptions;
        $this->options = [];
        $this->optionArray = [];
    }

    public function toOptionArray(): array
    {
        if (!$this->optionArray) {
            foreach ($this->loadOptions() as $value => $label) {
                $this->optionArray[] = compact('value', 'label');
            }
        }

        return $this->optionArray;
    }

    public function getOptionText(string $state): ?string
    {
        return isset($this->loadOptions()[$state]) ? (string) $this->loadOptions()[$state] : null;
    }

    private function loadOptions(): array
    {
        if (!$this->options) {
            $this->options = array_merge(
                [
                    ActionEntityInterface::STATE_PENDING => new Phrase('Pending'),
                    ActionEntityInterface::STATE_PROCESSING => new Phrase('Processing'),
                    ActionEntityInterface::STATE_CANCELED => new Phrase('Canceled'),
                    ActionEntityInterface::STATE_SUCCEEDED => new Phrase('Succeeded'),
                    ActionEntityInterface::STATE_FAILED => new Phrase('Failed'),
                ],
                $this->additionalOptions
            );
        }

        return $this->options;
    }
}
