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

final class Anonymous implements AnonymizerInterface
{
    /**
     * Constants for anonymous anonymizer
     */
    private const PHRASE = '%1Anonymous%2';
    private const PREFIX_LENGTH = 3;
    private const SUFFIX_LENGTH = 2;

    /**
     * @var Random
     */
    private $mathRandom;

    public function __construct(
        Random $mathRandom
    ) {
        $this->mathRandom = $mathRandom;
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function anonymize($value): string
    {
        $phrase = new Phrase(
            self::PHRASE,
            [
                $this->mathRandom->getRandomString(self::PREFIX_LENGTH),
                $this->mathRandom->getRandomString(self::SUFFIX_LENGTH),
            ]
        );

        return $phrase->render();
    }
}
