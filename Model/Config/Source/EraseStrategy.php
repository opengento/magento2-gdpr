<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Service\ErasureStrategy;

/**
 * Erase Strategy Config Data Source
 */
class EraseStrategy implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => ErasureStrategy::STRATEGY_DELETE,
                'label' => new Phrase('Delete')
            ],
            [
                'value' => ErasureStrategy::STRATEGY_ANONYMIZE,
                'label' => new Phrase('Anonymize')
            ],
        ];
    }
}
