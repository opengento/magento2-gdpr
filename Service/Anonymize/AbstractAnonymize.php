<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize;

use Magento\Framework\Math\Random;
use Magento\Framework\Phrase;

/**
 * Class AbstractAnonymize
 */
abstract class AbstractAnonymize implements ProcessorInterface
{
    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $mathRandom;

    /**
     * AbstractAnonymize constructor.
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
     * @return string
     */
    protected function anonymousValue(): string
    {
        return (new Phrase('Anonymous'))->render();
    }

    /**
     * Retrieve a random value
     *
     * @param int $length
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function randomValue(int $length = 10): string
    {
        return $this->mathRandom->getRandomString($length);
    }
}
