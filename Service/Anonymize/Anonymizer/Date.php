<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Anonymizer;

use Exception;
use Magento\Framework\Math\Random;
use Magento\Framework\Stdlib\DateTime;
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;

final class Date implements AnonymizerInterface
{
    /**
     * Constants for date anonymizer
     */
    private const MIN_TIMESTAMP = 0;
    private const MAX_TIMESTAMP = 1557480188;

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function anonymize($value): string
    {
        $dateTime = new \DateTime();
        $dateTime->setTimestamp(Random::getRandomNumber(self::MIN_TIMESTAMP, self::MAX_TIMESTAMP));

        return $dateTime->format(DateTime::DATE_PHP_FORMAT);
    }
}
