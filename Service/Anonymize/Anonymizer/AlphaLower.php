<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Anonymizer;

use Magento\Framework\Math\Random;
use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;

/**
 * Class AlphaLower
 */
final class AlphaLower implements AnonymizerInterface
{
    /**#@+
     * Constants for alpha lower anonymizer
     */
    private const DEFAULT_LENGTH = 5;
    /**#@-*/

    /**
     * @var \Magento\Framework\Math\Random
     */
    private $mathRandom;

    /**
     * @var int
     */
    private $length;

    /**
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param int $length
     */
    public function __construct(
        Random $mathRandom,
        int $length = self::DEFAULT_LENGTH
    ) {
        $this->mathRandom = $mathRandom;
        $this->length = $length;
    }

    /**
     * @inheritdoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function anonymize($value): string
    {
        return $this->mathRandom->getRandomString($this->length, Random::CHARS_LOWERS);
    }
}
