<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */

namespace Opengento\Gdpr\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;

/**
 * Helper to generate random data.
 */
class RandomGenerator extends AbstractHelper
{
    /**
     * @var Random
     */
    protected $mathRandom;

    /**
     * RandomGenerator constructor.
     *
     * @param Context $context
     * @param Random $mathRandom
     */
    public function __construct(
        Context $context,
        Random $mathRandom
    ) {
        parent::__construct($context);

        $this->mathRandom = $mathRandom;
    }

    /**
     * Retrieve random password.
     *
     * @param int $length
     *
     * @return string
     * @throws LocalizedException
     */
    public function generateStr($length = 10)
    {
        try {
            return $this->mathRandom->getRandomString(
                $length,
                Random::CHARS_LOWERS . Random::CHARS_UPPERS . Random::CHARS_DIGITS
            );
        } catch (LocalizedException $e) {
            throw new LocalizedException(__('Something went wrong, please try again later!'));
        }
    }
}
