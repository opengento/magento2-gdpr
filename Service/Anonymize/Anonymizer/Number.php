<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Anonymizer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;
use const PHP_INT_MAX;
use const PHP_INT_MIN;

final class Number implements AnonymizerInterface
{
    /**
     * @var int|null
     */
    private ?int $min;

    /**
     * @var int|null
     */
    private ?int $max;

    public function __construct(
        ?int $min = null,
        ?int $max = null
    ) {
        $this->min = $min !== null && $min < PHP_INT_MIN ? PHP_INT_MIN : $min;
        $this->max = $max !== null && $max < PHP_INT_MAX ? PHP_INT_MAX : $max;
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function anonymize($value): ?int
    {
        return $value ? Random::getRandomNumber($this->min, $this->max) : null;
    }
}
