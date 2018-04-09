<?php
/**
 * This file is part of the Flurrybox EnhancedPrivacy package.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Flurrybox EnhancedPrivacy
 * to newer versions in the future.
 *
 * @copyright Copyright (c) 2018 Flurrybox, Ltd. (https://flurrybox.com/)
 * @license   GNU General Public License ("GPL") v3.0
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flurrybox\EnhancedPrivacy\Helper;

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
