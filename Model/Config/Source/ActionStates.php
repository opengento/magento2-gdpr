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

    public function __construct(
        array $additionalOptions = []
    ) {
        $this->additionalOptions = $additionalOptions;
        $this->options = [];
    }

    public function toOptionArray(): array
    {
        if (!$this->options) {
            $this->options = array_merge(
                [
                    ['label' => new Phrase('Pending'), 'value' => ActionEntityInterface::STATE_PENDING],
                    ['label' => new Phrase('Processing'), 'value' => ActionEntityInterface::STATE_PROCESSING],
                    ['label' => new Phrase('Canceled'), 'value' => ActionEntityInterface::STATE_CANCELED],
                    ['label' => new Phrase('Succeeded'), 'value' => ActionEntityInterface::STATE_SUCCEEDED],
                    ['label' => new Phrase('Failed'), 'value' => ActionEntityInterface::STATE_FAILED],
                ],
                $this->additionalOptions
            );
        }

        return $this->options;
    }
}
