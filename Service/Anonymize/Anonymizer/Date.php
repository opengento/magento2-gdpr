<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Anonymizer;

use DateTime;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;

class Date implements AnonymizerInterface
{
    private const MIN_TIMESTAMP = 0;
    private const MAX_TIMESTAMP = 1557480188;

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function anonymize($value): ?string
    {
        return $value ? $this->randomDateTime()->format(StdlibDateTime::DATETIME_PHP_FORMAT) : null;
    }

    /**
     * @return DateTime
     * @throws LocalizedException
     */
    private function randomDateTime(): DateTime
    {
        $dateTime = new DateTime();
        $dateTime->setTimestamp(Random::getRandomNumber(self::MIN_TIMESTAMP, self::MAX_TIMESTAMP));

        return $dateTime;
    }
}
