<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Anonymize\Anonymizer;

use Opengento\Gdpr\Service\Anonymize\AnonymizerInterface;

/**
 * Class Phone
 */
class Phone implements AnonymizerInterface
{
    /**#@+
     * Constants for phone number anonymizer
     */
    private const PHONE_NUMBER = '9999999999';
    /**#@-*/

    /**
     * @inheritdoc
     */
    public function anonymize($value): string
    {
        return self::PHONE_NUMBER;
    }
}
