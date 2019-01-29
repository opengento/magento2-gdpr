<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize;

use Magento\Framework\Math\Random;
use Magento\Framework\Phrase;

/**
 * Class AnonymizeTool
 */
final class AnonymizeTool
{
    /**
     * @var \Magento\Framework\Math\Random
     */
    private $mathRandom;

    /**
     * @param \Magento\Framework\Math\Random $mathRandom
     */
    public function __construct(
        Random $mathRandom
    ) {
        $this->mathRandom = $mathRandom;
    }

    /**
     * Retrieve an anonymous value
     *
     * @param string $prefix [optional]
     * @param string $suffix [optional]
     * @return string
     */
    public function anonymousValue(string $prefix = '', string $suffix = ''): string
    {
        return (new Phrase('%1Anonymous%2', [$prefix, $suffix]))->render();
    }

    /**
     * Retrieve an anonymous email
     *
     * @param string $prefix [optional]
     * @param string $suffix [optional]
     * @return string
     */
    public function anonymousEmail(string $prefix = '', string $suffix = ''): string
    {
        return (new Phrase('%1anonymous%2@gdpr.org', [$prefix, $suffix]))->render();
    }

    /**
     * Retrieve anonymous phone number
     *
     * @return string
     */
    public function anonymousPhone(): string
    {
        return '9999999999';
    }

    /**
     * Retrieve a random value
     *
     * @param int $length
     * @param null|string $chars
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function randomValue(int $length = 10, string $chars = ''): string
    {
        return $this->mathRandom->getRandomString($length, $chars ?: null);
    }
}
