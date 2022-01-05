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
use function sprintf;

final class Street implements AnonymizerInterface
{
    private const STREET_LENGTH = 5;
    private const MIN_NUM = 0;
    private const MAX_NUM = 100;

    /**
     * @var Random
     */
    private Random $random;

    public function __construct(
        Random $random
    ) {
        $this->random = $random;
    }

    /**
     * @throws LocalizedException
     */
    public function anonymize($value): array
    {
        return [sprintf(
            '%s %s',
            Random::getRandomNumber(self::MIN_NUM, self::MAX_NUM),
            $this->random->getRandomString(self::STREET_LENGTH)
        )];
    }
}
