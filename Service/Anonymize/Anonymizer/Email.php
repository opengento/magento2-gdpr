<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Anonymizer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;

class Email implements AnonymizerInterface
{
    public function __construct(private Random $mathRandom) {}

    /**
     * @throws LocalizedException
     */
    public function anonymize($value): ?string
    {
        return $value ? $this->createPhrase()->render() : null;
    }

    /**
     * @throws LocalizedException
     */
    private function createPhrase(): Phrase
    {
        return new Phrase(
            '%1-anonymous-%2@gdpr.org',
            [
                $this->mathRandom->getRandomString(3, Random::CHARS_LOWERS),
                $this->mathRandom->getRandomString(2, Random::CHARS_LOWERS)
            ]
        );
    }
}
