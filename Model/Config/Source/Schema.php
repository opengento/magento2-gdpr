<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Phrase;

/**
 * Account delete schema types.
 */
class Schema implements OptionSourceInterface
{
    const DELETE = 0;
    const ANONYMIZE = 1;
    const DELETE_ANONYMIZE = 2;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::DELETE,
                'label' => new Phrase('Always delete')
            ],
            [
                'value' => self::ANONYMIZE,
                'label' => new Phrase('Always anonymize')
            ],
            [
                'value' => self::DELETE_ANONYMIZE,
                'label' => new Phrase('Delete if no orders made, anonymize otherwise')
            ]
        ];
    }
}
